<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlTenderDetailJob;
use App\Jobs\CrawlTenderDateJob;
use App\Jobs\CrawlTenderHsmtJob;
use App\Jobs\CrawlTenderSubResourceJob;
use App\Models\CrawlTask;
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
        $logs = $task->logs()->get();
        $classifier = new LogClassifier();

        $queued = 0;
        $skipped_success = 0;
        $skipped_running = 0;
        $failed_infer = 0;
        $items = [];
        $seenDatePages = [];

        foreach ($logs as $log) {
            $ctx = $log->context ?? [];
            $derived = $classifier->classify([
                'level' => $log->level,
                'message' => $log->message,
                'context' => $ctx,
            ]);

            if ($derived['status'] !== 'failed') {
                continue;
            }

            // Attempt to infer job type and parameters
            $inferred = $this->inferJobFromContext($ctx, $log->message);

            if (!$inferred) {
                $failed_infer++;
                $items[] = ['log_id' => $log->id, 'status' => 'infer_failed'];
                continue;
            }

            [$jobClass, $params, $queue] = $inferred;

            // For CrawlTenderDateJob enforce dedupe by date+page to avoid re-dispatching same page multiple times
            if ($jobClass === CrawlTenderDateJob::class) {
                $d = $params['date'] ?? null;
                $p = isset($params['page']) ? (int) $params['page'] : 0;
                if (!$d) {
                    $failed_infer++;
                    $items[] = ['log_id' => $log->id, 'status' => 'infer_failed'];
                    continue;
                }
                $retryKey = "{$d}_{$p}";
                if (isset($seenDatePages[$retryKey])) {
                    // already queued for this date+page
                    $items[] = ['log_id' => $log->id, 'status' => 'skipped_duplicate_date_page'];
                    continue;
                }
                // mark seen to prevent duplicate dispatch
                $seenDatePages[$retryKey] = true;
            }

            // simple dedupe: if there is an existing success log for same job params, skip
            $hasSuccess = $task->logs()->get()->filter(function ($l) use ($classifier, $params) {
                $d = $classifier->classify(['level' => $l->level, 'message' => $l->message, 'context' => $l->context ?? []]);
                if ($d['status'] !== 'success') return false;
                $c = $l->context ?? [];
                return $this->paramsMatch($c, $params);
            })->count() > 0;

            if ($hasSuccess) {
                $skipped_success++;
                $items[] = ['log_id' => $log->id, 'status' => 'skipped_already_success'];
                continue;
            }

            // skip if running
            $isRunning = $task->logs()->get()->filter(function ($l) use ($classifier, $params) {
                $d = $classifier->classify(['level' => $l->level, 'message' => $l->message, 'context' => $l->context ?? []]);
                if ($d['status'] !== 'running') return false;
                $c = $l->context ?? [];
                return $this->paramsMatch($c, $params);
            })->count() > 0;

            if ($isRunning) {
                $skipped_running++;
                $items[] = ['log_id' => $log->id, 'status' => 'skipped_running'];
                continue;
            }

            try {
                // Dispatch known job types
                if ($jobClass === CrawlTenderDetailJob::class) {
                    dispatch(new CrawlTenderDetailJob($params['tender_id'], $task->id))->onQueue($queue ?: 'detail');
                } elseif ($jobClass === CrawlTenderSubResourceJob::class) {
                    dispatch(new CrawlTenderSubResourceJob($params['tender_id'], $params['type'], $task->id))->onQueue($queue ?: 'sub');
                } elseif ($jobClass === CrawlTenderHsmtJob::class) {
                    dispatch(new CrawlTenderHsmtJob($params['tender_id'], $task->id))->onQueue($queue ?: 'hsmt');
                } elseif ($jobClass === CrawlTenderDateJob::class) {
                    // CrawlTenderDateJob constructor: __construct(string $date, int $page, int $taskId)
                    $date = $params['date'];
                    $page = isset($params['page']) ? (int) $params['page'] : 0;
                    dispatch(new CrawlTenderDateJob($date, $page, $task->id))->onQueue($queue ?: 'crawl');
                } else {
                    // unknown but inferred
                    $failed_infer++;
                    $items[] = ['log_id' => $log->id, 'status' => 'infer_failed'];
                    continue;
                }

                $queued++;
                $items[] = ['log_id' => $log->id, 'status' => 'queued'];
            } catch (\Throwable $e) {
                $items[] = ['log_id' => $log->id, 'status' => 'dispatch_failed', 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'queued' => $queued,
            'skipped_success' => $skipped_success,
            'skipped_running' => $skipped_running,
            'failed_infer' => $failed_infer,
            'items' => $items,
        ]);
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
