<?php

namespace App\Http\Controllers;

use App\Models\CrawlTask;
use App\Services\LogClassifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrawlTaskLogController extends Controller
{
    public function logs(Request $request, CrawlTask $task): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 200);
        $allowed = [25, 50, 100, 200, 500];
        if (!in_array($perPage, $allowed)) {
            $perPage = 200;
        }

        $page = (int) $request->get('page', 1);

        // allow server-side lifecycle filtering (e.g. failed, running)
        $lifecycle = (string) $request->get('lifecycle', 'all');

        $logsQuery = $task->logs();

        if ($lifecycle && $lifecycle !== 'all') {
            if ($lifecycle === 'failed') {
                $logsQuery = $logsQuery->where(function ($q) {
                    $q->where('level', 'error')
                        ->orWhere('message', 'LIKE', '%FAILED%')
                        ->orWhere('message', 'LIKE', '%ERROR%')
                        ->orWhere('context->event_type', 'LIKE', '%FAILED%')
                        ->orWhere('context->event_type', 'LIKE', '%ERROR%')
                        ->orWhere('context->status', 'failed');
                });
            } elseif ($lifecycle === 'running') {
                $logsQuery = $logsQuery->where(function ($q) {
                    $q->where('context->status', 'running')
                        ->orWhere('message', 'LIKE', '%START%')
                        ->orWhere('message', 'LIKE', '%RUN%')
                        ->orWhere('context->event_type', 'LIKE', '%START%')
                        ->orWhere('context->event_type', 'LIKE', '%RUN%');
                });
            }
        }

        $logsQuery = $logsQuery->latest('created_at');
        $paginator = $logsQuery->paginate($perPage, ['*'], 'page', $page);
        $logsEloquent = $paginator->getCollection();

        $classifier = new LogClassifier();

        $logs = $logsEloquent->map(function ($log) use ($classifier) {
            $row = [
                'id' => $log->id,
                'queue' => $log->queue,
                'level' => $log->level,
                'message' => $log->message,
                'context' => $log->context,
                'created_at' => $log->created_at->toDateTimeString(),
            ];

            $ctx = $log->context ?? [];

            // prefer explicit context.status if present
            if (!empty($ctx['status'])) {
                $derived = [
                    'status' => strtolower((string) $ctx['status']),
                    'event_type' => $ctx['event_type'] ?? null,
                    'is_terminal' => in_array(strtolower((string) $ctx['status']), ['success', 'failed', 'done', 'finished']),
                ];
            } else {
                $derived = $classifier->classify([
                    'level' => $log->level,
                    'message' => $log->message,
                    'context' => $ctx,
                ]);
            }

            $row['derived_status'] = $derived['status'];
            $row['event_type'] = $derived['event_type'] ?? null;
            $row['is_terminal'] = (bool) ($derived['is_terminal'] ?? false);

            return $row;
        })->values();

        // Compute summary over ALL logs (not just current page) for accurate metrics
        $allLogs = $task->logs()->get();

        $processed = 0;
        $success = 0;
        $failed = 0;
        $timeout = 0;
        $totalPackages = null;

        $lifecycleCounts = [
            'running' => 0,
            'success' => 0,
            'failed' => 0,
            'info' => 0,
        ];

        foreach ($allLogs as $l) {
            $ctx = $l->context ?? [];
            if (is_array($ctx)) {
                $processed += isset($ctx['processed_packages']) ? (int) $ctx['processed_packages'] : 0;
                $success += isset($ctx['success_packages']) ? (int) $ctx['success_packages'] : 0;
                $failed += isset($ctx['failed_packages']) ? (int) $ctx['failed_packages'] : 0;
                $timeout += isset($ctx['timeout_packages']) ? (int) $ctx['timeout_packages'] : 0;
                if ($totalPackages === null && isset($ctx['total_packages'])) {
                    $totalPackages = (int) $ctx['total_packages'];
                }
                if (isset($ctx['error_type']) && $ctx['error_type'] === 'timeout') {
                    $timeout += 1;
                }
            }

            // derive lifecycle status for counts (prefer explicit ctx.status)
            $derived = null;
            if (is_array($ctx) && !empty($ctx['status'])) {
                $derived = strtolower((string) $ctx['status']);
            } else {
                $d = $classifier->classify([
                    'level' => $l->level,
                    'message' => $l->message,
                    'context' => $ctx,
                ]);
                $derived = $d['status'] ?? 'info';
            }

            if (isset($lifecycleCounts[$derived])) {
                $lifecycleCounts[$derived]++;
            } else {
                $lifecycleCounts['info']++;
            }
        }

        $totalLogs = $allLogs->count();

        $summary = [
            'total' => $totalLogs,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'timeout' => $timeout,
            'progress_percent' => ($totalPackages && $totalPackages > 0) ? (int) round(($processed / $totalPackages) * 100) : 0,
            'lifecycle_counts' => $lifecycleCounts,
        ];

        return response()->json([
            'logs' => $logs,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'summary' => $summary,
        ]);
    }
}
