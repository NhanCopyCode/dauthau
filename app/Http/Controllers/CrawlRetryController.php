<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlTenderDetailJob;
use App\Jobs\CrawlTenderDateJob;
use App\Jobs\CrawlTenderHsmtJob;
use App\Jobs\CrawlTenderSubResourceJob;
use App\Models\CrawlTask;
use App\Services\CrawlTracker;
use App\Services\LogClassifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrawlRetryController extends Controller
{
    public function candidates(CrawlTask $task): JsonResponse
    {
        $logs = $task->logs()->get();
        $classifier = new LogClassifier();

        $failed = [];

        foreach ($logs as $log) {
            $ctx = $log->context ?? [];
            $derived = $classifier->classify([
                'level' => $log->level,
                'message' => $log->message,
                'context' => $ctx,
            ]);

            if ($derived['status'] === 'failed') {
                $failed[] = [
                    'id' => $log->id,
                    'message' => $log->message,
                    'context' => $ctx,
                    'created_at' => $log->created_at->toDateTimeString(),
                ];
            }
        }

        return response()->json(['candidates' => $failed]);
    }

    public function retry(Request $request, CrawlTask $task): JsonResponse
    {
        // ── CRITICAL: Update status BEFORE dispatching ──────────────
        // This avoids a race condition where retried jobs finish before
        // we save, causing checkCompletion() to see stale failed_items
        // and mark completed_logged=true, which then prevents the
        // subsequent retry save from "taking" and leaves the task stuck.
        $task->status = CrawlTask::STATUS_RUNNING;
        $task->started_at = now();
        $task->finished_at = null;
        $task->error = null;
        $task->failed_items = 0;
        $task->save();
        $task->refresh();

        $result = $this->retryForTask($task);
        $queued = (int) ($result['queued'] ?? 0);
        $skippedSuccess = (int) ($result['skipped_success'] ?? 0);
        $skippedRunning = (int) ($result['skipped_running'] ?? 0);
        $failedInfer = (int) ($result['failed_infer'] ?? 0);

        // ── Fallback: if no jobs were dispatched, re-evaluate ──────
        // retryForTask() always starts the CrawlTracker, so if $queued=0
        // the tracker's markProducerDone() should have already called
        // checkCompletion(). However, if that failed (e.g. lock issue),
        // we fix the status here as a safety net.
        if ($queued === 0) {
            $task->refresh();

            // All failures resolved → completed
            if ($task->status === CrawlTask::STATUS_RUNNING) {
                if ($skippedSuccess > 0 || ($skippedRunning === 0 && $failedInfer === 0)) {
                    $task->status = CrawlTask::STATUS_COMPLETED;
                    $task->finished_at = now();
                    $task->save();
                } elseif ($failedInfer > 0) {
                    // Some jobs couldn't be inferred, no jobs dispatched
                    // Mark as completed_with_errors so it doesn't hang
                    $task->status = CrawlTask::STATUS_COMPLETED_WITH_ERRORS;
                    $task->finished_at = now();
                    $task->save();
                }
                // If skipped_running > 0 but nothing queued → task should
                // already be in a running state from original crawl; leave it
            }
        }

        return response()->json($result);
    }

    public function retryAll(Request $request): JsonResponse
    {
        $tasks = CrawlTask::whereIn('status', ['failed', 'completed_with_errors'])->get();

        $totals = [
            'queued'          => 0,
            'skipped_success' => 0,
            'skipped_running' => 0,
            'failed_infer'    => 0,
            'tasks_processed' => $tasks->count(),
        ];

        foreach ($tasks as $task) {
            // ── Update status BEFORE dispatching (race-condition guard) ──
            $task->status = CrawlTask::STATUS_RUNNING;
            $task->started_at = now();
            $task->finished_at = null;
            $task->error = null;
            $task->failed_items = 0;
            $task->save();
            $task->refresh();

            $res = $this->retryForTask($task);
            $totals['queued'] += $res['queued'] ?? 0;
            $totals['skipped_success'] += $res['skipped_success'] ?? 0;
            $totals['skipped_running'] += $res['skipped_running'] ?? 0;
            $totals['failed_infer'] += $res['failed_infer'] ?? 0;

            // ── Fallback: if no jobs dispatched, re-evaluate ──────
            $queued = (int) ($res['queued'] ?? 0);
            $skippedSuccess = (int) ($res['skipped_success'] ?? 0);
            $skippedRunning = (int) ($res['skipped_running'] ?? 0);
            $failedInfer = (int) ($res['failed_infer'] ?? 0);

            if ($queued === 0) {
                $task->refresh();
                if ($task->status === CrawlTask::STATUS_RUNNING) {
                    if ($skippedSuccess > 0 || ($skippedRunning === 0 && $failedInfer === 0)) {
                        $task->status = CrawlTask::STATUS_COMPLETED;
                        $task->finished_at = now();
                        $task->save();
                    } elseif ($failedInfer > 0) {
                        $task->status = CrawlTask::STATUS_COMPLETED_WITH_ERRORS;
                        $task->finished_at = now();
                        $task->save();
                    }
                }
            }
        }

        return response()->json($totals);
    }

    /**
     * Retry failed log-derived jobs for a single task and return a summary array.
     *
     * Optimised: loads logs ONCE, classifies all upfront, indexes by param signature
     * so we avoid O(n²) DB/collection scans for hasSuccess/isRunning checks.
     */
    protected function retryForTask(CrawlTask $task): array
    {
        // ── Load all logs ONCE ──────────────────────────────────────
        $allLogs = $task->logs()->get();
        $classifier = new LogClassifier();

        // Pre-classify every log and build indexed lookup structures.
        // Index key = params signature (date_page or tender_id).
        $successByKey = [];   // paramKey => true (exists a success log later)
        $runningByKey = [];   // paramKey => true (exists a running log later)
        $paramKeyOfLog = [];  // logId => paramKey

        $classified = [];     // logId => derived status

        foreach ($allLogs as $l) {
            $ctx = $l->context ?? [];
            $derived = $classifier->classify([
                'level'   => $l->level,
                'message' => $l->message,
                'context' => $ctx,
            ]);
            $classified[$l->id] = $derived['status'];
            $paramKeyOfLog[$l->id] = $this->makeParamKey($ctx);
        }

        // Build indexes: for each log, check if there's a later success/running log with same params
        foreach ($allLogs as $l) {
            $status = $classified[$l->id] ?? 'info';
            $key = $paramKeyOfLog[$l->id];

            if (!$key) continue;

            if ($status === 'success') {
                // Mark all earlier failed logs with the same key as having a success resolution
                if (!isset($successByKey[$key])) {
                    $successByKey[$key] = $l->created_at;
                }
            }
            if ($status === 'running') {
                if (!isset($runningByKey[$key])) {
                    $runningByKey[$key] = $l->created_at;
                }
            }
        }

        // Quick helper: does a param key have a success log after a given timestamp?
        $hasLaterSuccess = function (string $key, $afterTimestamp) use ($successByKey, $allLogs, $classified, $paramKeyOfLog): bool {
            $latestSuccessAt = $successByKey[$key] ?? null;
            if (!$latestSuccessAt) return false;
            // Find the earliest success log for this key that is strictly after $afterTimestamp
            foreach ($allLogs as $l) {
                if (empty($l->created_at)) continue;
                if ($l->created_at->lte($afterTimestamp)) continue;
                $k = $paramKeyOfLog[$l->id] ?? '';
                if ($k !== $key) continue;
                $s = $classified[$l->id] ?? '';
                if ($s === 'success') return true;
            }
            return false;
        };

        $hasLaterRunning = function (string $key, $afterTimestamp) use ($runningByKey, $allLogs, $classified, $paramKeyOfLog): bool {
            $latestRunningAt = $runningByKey[$key] ?? null;
            if (!$latestRunningAt) return false;
            foreach ($allLogs as $l) {
                if (empty($l->created_at)) continue;
                if ($l->created_at->lte($afterTimestamp)) continue;
                $k = $paramKeyOfLog[$l->id] ?? '';
                if ($k !== $key) continue;
                $s = $classified[$l->id] ?? '';
                if ($s === 'running') return true;
            }
            return false;
        };

        // ── Initialise CrawlTracker BEFORE dispatching any jobs ────
        // This avoids the race condition where a job finishes before the
        // tracker is ready, causing outstanding count to be wrong and the
        // task to never complete.
        $tracker = app(CrawlTracker::class);
        $tracker->start($task->id);

        // ── Process failed logs ─────────────────────────────────────
        $queued = 0;
        $skipped_success = 0;
        $skipped_running = 0;
        $failed_infer = 0;
        $seenDatePages = [];

        // Track logs whose stale 'running' context we've already resolved
        $resolvedStaleRunning = [];

        foreach ($allLogs as $log) {
            $status = $classified[$log->id] ?? 'info';
            if ($status !== 'failed') continue;

            $ctx = $log->context ?? [];
            $inferred = $this->inferJobFromContext($ctx, $log->message);

            if (!$inferred) {
                $failed_infer++;
                continue;
            }

            [$jobClass, $params, $queue] = $inferred;

            if ($jobClass === CrawlTenderDateJob::class) {
                $d = $params['date'] ?? null;
                $p = isset($params['page']) ? (int) $params['page'] : 0;
                if (!$d) {
                    $failed_infer++;
                    continue;
                }
                $retryKey = "{$d}_{$p}";
                if (isset($seenDatePages[$retryKey])) {
                    continue;
                }
                $seenDatePages[$retryKey] = true;
            }

            $paramKey = $this->makeParamKey($params);

            // ── Resolve stale 'running' logs ────────────────────────
            // Mark any 'running' log with matching params as resolved
            // so the isRunning check below doesn't block the retry.
            foreach ($allLogs as $l) {
                if (isset($resolvedStaleRunning[$l->id])) continue;
                $s = $classified[$l->id] ?? '';
                if ($s !== 'running') continue;
                $lk = $paramKeyOfLog[$l->id] ?? '';
                if ($lk !== $paramKey) continue;
                if (empty($l->created_at)) continue;

                $lCtx = $l->context ?? [];
                $lCtx['status'] = 'info';
                $lCtx['resolved_by_retry'] = true;
                $lCtx['resolved_at'] = now()->toDateTimeString();
                $l->context = $lCtx;
                $l->save();
                $resolvedStaleRunning[$l->id] = true;
            }

            // ── Check if already succeeded later ────────────────────
            if ($paramKey && $hasLaterSuccess($paramKey, $log->created_at)) {
                $ctx['status'] = 'info';
                $ctx['resolved_by_retry'] = true;
                $ctx['resolved_at'] = now()->toDateTimeString();
                $log->level = 'info';
                $log->context = $ctx;
                $log->save();

                $skipped_success++;
                continue;
            }

            // ── Check if already running later ──────────────────────
            if ($paramKey && $hasLaterRunning($paramKey, $log->created_at)) {
                $skipped_running++;
                continue;
            }

            // ── Dispatch the retry job ──────────────────────────────
            try {
                if ($jobClass === CrawlTenderDetailJob::class) {
                    dispatch(new CrawlTenderDetailJob($params['tender_id'], $task->id))->onQueue($queue ?: 'detail');
                } elseif ($jobClass === CrawlTenderSubResourceJob::class) {
                    dispatch(new CrawlTenderSubResourceJob($params['tender_id'], $params['type'], $task->id))->onQueue($queue ?: 'sub');
                } elseif ($jobClass === CrawlTenderHsmtJob::class) {
                    dispatch(new CrawlTenderHsmtJob($params['tender_id'], $task->id))->onQueue($queue ?: 'hsmt');
                } elseif ($jobClass === CrawlTenderDateJob::class) {
                    $date = $params['date'];
                    $page = isset($params['page']) ? (int) $params['page'] : 0;
                    dispatch(new CrawlTenderDateJob($date, $page, $task->id))->onQueue($queue ?: 'crawl');
                } else {
                    $failed_infer++;
                    continue;
                }

                // Register with tracker immediately after dispatch
                $tracker->jobDispatched($task->id);
                $queued++;
            } catch (\Throwable $e) {
                // Individual dispatch failure — just count it, don't bloat response
                $failed_infer++;
            }
        }

        // ── Mark producer done (no more jobs from this batch) ───────
        $tracker->markProducerDone($task->id);

        return [
            'queued'          => $queued,
            'skipped_success' => $skipped_success,
            'skipped_running' => $skipped_running,
            'failed_infer'    => $failed_infer,
        ];
    }

    /**
     * Build a deterministic parameter key for a context/params array.
     * Returns e.g. "date:2024-01-15_page:0" or "tender:12345".
     */
    protected function makeParamKey(array $ctx): ?string
    {
        if (isset($ctx['date'])) {
            $page = isset($ctx['page']) ? (int) $ctx['page'] : 0;
            return "date:{$ctx['date']}_page:{$page}";
        }
        if (isset($ctx['tender_id'])) {
            return 'tender:' . ((int) $ctx['tender_id']);
        }
        if (isset($ctx['tender']) && is_array($ctx['tender']) && isset($ctx['tender']['id'])) {
            return 'tender:' . ((int) $ctx['tender']['id']);
        }
        if (isset($ctx['payload']) && is_array($ctx['payload']) && isset($ctx['payload']['tender_id'])) {
            return 'tender:' . ((int) $ctx['payload']['tender_id']);
        }
        return null;
    }

    public function zombieCheck(Request $request, CrawlTask $task): JsonResponse
    {
        // threshold minutes (default 15)
        $threshold = (int) env('CRAWL_ZOMBIE_THRESHOLD_MINUTES', 15);

        $lastLog = $task->logs()->orderBy('created_at', 'desc')->first();

        if (!$lastLog) {
            return response()->json(['possible_zombie' => false, 'reason' => 'no_logs']);
        }

        $lastAt = $lastLog->created_at;
        $diff = now()->diffInMinutes($lastAt);

        $possible = $diff >= $threshold;

        return response()->json([
            'possible_zombie' => $possible,
            'last_log_at' => $lastAt->toDateTimeString(),
            'minutes_since_last_log' => $diff,
            'threshold_minutes' => $threshold,
        ]);
    }

    protected function inferJobFromContext(array $ctx, string $message): ?array
    {
        // Look for tender_id in common places
        $tenderId = null;
        if (isset($ctx['tender_id']) && is_numeric($ctx['tender_id'])) $tenderId = (int) $ctx['tender_id'];
        if (empty($tenderId) && isset($ctx['tender']) && is_array($ctx['tender']) && isset($ctx['tender']['id'])) $tenderId = (int) $ctx['tender']['id'];
        if (empty($tenderId) && isset($ctx['payload']) && is_array($ctx['payload']) && isset($ctx['payload']['tender_id'])) $tenderId = (int) $ctx['payload']['tender_id'];

        // job_name may be present in context
        $jobName = isset($ctx['job_name']) ? $ctx['job_name'] : null;

        // Infer CrawlTenderDateJob
        if ($jobName && stripos($jobName, 'CrawlTenderDateJob') !== false) {
            $date = $ctx['date'] ?? null;
            $page = isset($ctx['page']) ? (int) $ctx['page'] : 0;
            if ($date) return [CrawlTenderDateJob::class, ['date' => $date, 'page' => $page], 'crawl'];
            return null;
        }

        // Infer CrawlTenderDetailJob
        if ($jobName && stripos($jobName, 'CrawlTenderDetailJob') !== false) {
            if ($tenderId) return [CrawlTenderDetailJob::class, ['tender_id' => $tenderId], 'detail'];
            return null;
        }

        // Infer sub resource
        if ($jobName && stripos($jobName, 'CrawlTenderSubResourceJob') !== false) {
            // need type
            $type = $ctx['type'] ?? ($ctx['payload']['type'] ?? null);
            if ($tenderId && $type) return [CrawlTenderSubResourceJob::class, ['tender_id' => $tenderId, 'type' => $type], 'sub'];
            return null;
        }

        // Infer hsmt
        if ($jobName && stripos($jobName, 'CrawlTenderHsmtJob') !== false) {
            if ($tenderId) return [CrawlTenderHsmtJob::class, ['tender_id' => $tenderId], 'hsmt'];
            return null;
        }

        // Fallback: try message content for known patterns
        if (str_contains(strtoupper($message), 'DETAIL') && $tenderId) {
            return [CrawlTenderDetailJob::class, ['tender_id' => $tenderId], 'detail'];
        }

        return null;
    }

    protected function paramsMatch(array $context, array $params): bool
    {
        // Match by date/page when present
        if (isset($params['date'])) {
            $ctxDate = $context['date'] ?? null;
            $ctxPage = isset($context['page']) ? (int) $context['page'] : 0;
            $pDate = $params['date'];
            $pPage = isset($params['page']) ? (int) $params['page'] : 0;
            if ($ctxDate && $ctxDate == $pDate && $ctxPage === $pPage) return true;
        }

        if (isset($params['tender_id'])) {
            if (isset($context['tender_id']) && (int) $context['tender_id'] === (int) $params['tender_id']) return true;
            if (isset($context['tender']) && is_array($context['tender']) && isset($context['tender']['id']) && (int) $context['tender']['id'] === (int) $params['tender_id']) return true;
            if (isset($context['payload']) && is_array($context['payload']) && isset($context['payload']['tender_id']) && (int) $context['payload']['tender_id'] === (int) $params['tender_id']) return true;
        }
        return false;
    }
}
