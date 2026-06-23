<?php

namespace App\Http\Controllers;

use App\Models\CrawlTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrawlTaskDetailController extends Controller
{
    public function show(Request $request, CrawlTask $task): JsonResponse
    {
        // Build improved task summary for UI/UX
        $startedAt = $task->started_at;
        $finishedAt = $task->finished_at;

        $durationSeconds = null;
        if ($startedAt && $finishedAt) {
            $durationSeconds = abs($finishedAt->diffInSeconds($startedAt));
        }

        $displayTypeMap = [
            'range' => 'Range Crawl',
            'daily' => 'Daily Crawl',
            'full' => 'Full Crawl',
        ];

        $displayStatusMap = [
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'completed_with_errors' => 'Completed (with errors)',
            'failed' => 'Failed',
        ];

        $taskData = [
            'id' => $task->id,
            'type' => $task->type,
            'display_type' => $displayTypeMap[$task->type] ?? ucfirst($task->type),
            'status' => $task->status,
            'display_status' => $displayStatusMap[$task->status] ?? ucfirst($task->status),
            'started_at' => optional($startedAt)->toIso8601String(),
            'finished_at' => optional($finishedAt)->toIso8601String(),
            'duration_seconds' => $durationSeconds,
            'crawl_range' => [
                'from' => optional($task->from_date)->format('Y-m-d'),
                'to' => optional($task->to_date)->format('Y-m-d'),
            ],
            'error' => $task->error,
            'failed_items' => (int) ($task->failed_items ?? 0),
        ];

        // Metrics
        $processedItems = (int) ($task->processed_items ?? 0);
        // Use API-reported total (number of tenders) for display
        $totalItems = (int) ($task->api_total_items ?? 0);
        // guard: total should not be less than processed for display
        if ($totalItems < $processedItems) {
            $totalItems = $processedItems;
        }

        $itemsPercent = $totalItems > 0 ? (int) round($processedItems / $totalItems * 100) : 0;

        $processedPages = (int) ($task->processed_pages ?? 0);
        $totalPages = (int) ($task->total_pages ?? 0);
        if ($totalPages < $processedPages) $totalPages = $processedPages;
        $pagesPercent = $totalPages > 0 ? (int) round($processedPages / $totalPages * 100) : 0;

        // Aggregate log level counts (no timeline/pagination returned)
        $levels = ['info' => 0, 'warning' => 0, 'error' => 0];
        try {
            $levelsRaw = $task->logs()
                ->selectRaw('LOWER(level) as lvl, COUNT(*) as cnt')
                ->groupBy('lvl')
                ->pluck('cnt', 'lvl')
                ->toArray();
            foreach ($levelsRaw as $k => $v) {
                $k = strtolower($k);
                $levels[$k] = (int) $v;
            }
        } catch (\Throwable $e) {
            // fallback: count in PHP to avoid 500 if DB grouping fails for any reason
            Log::warning('Failed to aggregate log levels via DB, falling back to in-memory count: ' . $e->getMessage());
            $logsAll = $task->logs()->get();
            foreach ($logsAll as $l) {
                $lvl = strtolower($l->level ?? 'info');
                if (!isset($levels[$lvl])) $levels[$lvl] = 0;
                $levels[$lvl]++;
            }
        }

        // Failure counters left as zeros for now (could be computed from logs if needed)
        $timeoutCount = 0;
        $errorCount = 0;
        $retryCount = 0;

        $metrics = [
            'items' => [
                'processed' => $processedItems,
                'total' => $totalItems,
                'percent' => $itemsPercent,
            ],
            'pages' => [
                'processed' => $processedPages,
                'total' => $totalPages,
                'percent' => $pagesPercent,
            ],
            'logs' => $levels,
            'failures' => [
                'timeout' => $timeoutCount,
                'error_count' => $errorCount,
                'retry_count' => $retryCount,
                'failed_items' => (int) ($task->failed_items ?? 0),
            ],
        ];

        // Provide debug logs only on explicit request
        $debugLogs = [];
        if ($request->boolean('debug', false)) {
            $debugLogs = $task->logs()->latest('created_at')->limit(50)->get()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'level' => $log->level,
                    'message' => $log->message,
                    'context' => $log->context,
                    'created_at' => optional($log->created_at)->format('Y-m-d H:i:s'),
                ];
            })->values();
        }

        return response()->json([
            'task' => $taskData,
            'metrics' => $metrics,
            'debug_logs' => $debugLogs,
        ]);
    }
}
