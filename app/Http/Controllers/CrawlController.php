<?php

namespace App\Http\Controllers;

use App\Jobs\RunCrawlTaskJob;
use App\Models\CrawlTask;
use App\Models\Tender;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CrawlController extends Controller
{
    public function full()
    {
        $task = CrawlTask::create([
            'type' => 'full',
            'status' => 'running',
            'started_at' => now(),
            'finished_at' => null,
            'processed_pages' => 0,
            'processed_items' => 0,
            'total_pages' => 0,
            'total_items' => 0,
            'api_total_items' => 0,
            'error' => null,
        ]);

        RunCrawlTaskJob::dispatch($task->id)
            ->onQueue('crawl');

        return response()->json([

            'success' => true,

            'task_id' => $task->id,
        ]);
    }

    public function daily()
    {
        $today = now()->toDateString();

        $task = CrawlTask::create([
            'type' => 'daily',
            'status' => 'running',
            'started_at' => now(),
            'finished_at' => null,
            'processed_pages' => 0,
            'processed_items' => 0,
            'total_pages' => 0,
            'total_items' => 0,
            'api_total_items' => 0,
            'error' => null,
            'from_date' => $today,
            'to_date' => $today,
        ]);

        RunCrawlTaskJob::dispatch(
            $task->id
        )->onQueue('crawl');

        return response()->json([

            'success' => true,

            'task_id' => $task->id,
        ]);
    }

    public function range(Request $request)
    {
        $request->validate([

            'from_date' => 'required|date',

            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $task = CrawlTask::create([
            'type' => 'range',
            'status' => 'running',
            'started_at' => now(),
            'finished_at' => null,
            'processed_pages' => 0,
            'processed_items' => 0,
            'total_pages' => 0,
            'total_items' => 0,
            'api_total_items' => 0,
            'error' => null,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ]);

        RunCrawlTaskJob::dispatch($task->id)
            ->onQueue('crawl');

        return response()->json([

            'success' => true,

            'task_id' => $task->id,
        ]);
    }


    public function history(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $allowed = [5, 10, 20, 50, 100];
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $query = CrawlTask::query();

        // Keyword search: id, type, error
        if ($keyword = trim($request->get('keyword', ''))) {
            $query->where(function ($q) use ($keyword) {
                if (is_numeric($keyword)) {
                    $q->orWhere('id', (int) $keyword);
                }
                $q->orWhere('type', 'LIKE', "%{$keyword}%")
                    ->orWhere('error', 'LIKE', "%{$keyword}%");
            });
        }

        // Status filter
        if ($status = $request->get('status')) {
            if (strtolower($status) !== 'all') {
                $query->where('status', $status);
            }
        }

        // Data range filter: filter tasks by the data-range they were configured to crawl
        // These correspond to the UI group "Khoảng dữ liệu crawl"
        if ($from = $request->get('from_date')) {
            try {
                $fromDt = Carbon::parse($from)->startOfDay();
                $query->whereDate('from_date', '>=', $fromDt->toDateString());
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        if ($to = $request->get('to_date')) {
            try {
                $toDt = Carbon::parse($to)->endOfDay();
                $query->whereDate('to_date', '<=', $toDt->toDateString());
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        // Crawl run time filter: filter tasks by when they actually ran (started_at)
        // These correspond to the UI group "Thời gian crawl" and are optional
        if ($startedFrom = $request->get('crawl_started_from')) {
            try {
                $startedFromDt = Carbon::parse($startedFrom)->startOfDay();
                $query->where('started_at', '>=', $startedFromDt);
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        if ($startedTo = $request->get('crawl_started_to')) {
            try {
                $startedToDt = Carbon::parse($startedTo)->endOfDay();
                $query->where('started_at', '<=', $startedToDt);
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        $paginator = $query->latest('started_at')
            ->paginate($perPage)
            ->appends($request->only(['keyword', 'status', 'from_date', 'to_date', 'crawl_started_from', 'crawl_started_to', 'per_page']));

        $items = collect($paginator->items())->map(function ($task) {
            return [
                'id' => $task->id,
                'type' => $task->type,
                'status' => $task->status,

                'from_date' => optional($task->from_date)->format('Y-m-d'),
                'to_date' => optional($task->to_date)->format('Y-m-d'),
                'started_at' => optional($task->started_at)->toIso8601String(),

                'finished_at' => optional($task->finished_at)->toIso8601String(),

                'processed_items' => $task->processed_items ?? 0,
                'failed_items' => $task->failed_items ?? 0,

                // surface the API-reported number of tenders for this task
                'total_items' => $task->api_total_items ?? 0,

                'progress' => (
                    ($task->processed_items ?? 0) . '/' . ($task->api_total_items ?? 0)
                ),

                'error' => $task->error,
            ];
        })->values();

        return response()->json([
            'tasks' => $items,
            'server_now' => now()->toIso8601String(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }


    public function stats()
    {
        $today = now('Asia/Ho_Chi_Minh')
            ->startOfDay();


        $totalItems = Tender::query()
            ->count();

        $todayItems = Tender::query()
            ->where('created_at', '>=', $today)
            ->count();


        $avgSeconds = CrawlTask::query()
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->selectRaw(
                'AVG(
                TIMESTAMPDIFF(
                    SECOND,
                    started_at,
                    finished_at
                )
            ) as avg_duration'
            )
            ->value('avg_duration');

        $avgFormatted = '--';

        if ($avgSeconds) {
            $minutes = floor($avgSeconds / 60);
            $seconds = $avgSeconds % 60;

            $avgFormatted =
                "{$minutes}m {$seconds}s";
        }

        $lastTask = CrawlTask::query()
            ->latest('started_at')
            ->first();

        $lastStatus =
            $lastTask?->status ?? '--';

        $lastTime =
            $lastTask?->started_at
            ? $lastTask
            ->started_at
            ->timezone(
                'Asia/Ho_Chi_Minh'
            )
            ->format('H:i d/m/Y')
            : '--';

        $runningJobs = CrawlTask::query()
            ->where('status', 'running')
            ->count();

        $currentProgress = '--';

        if ($runningJobs > 0) {
            $currentProgress = '⏳ Đang crawl...';
        } elseif ($lastTask && $lastTask->status === CrawlTask::STATUS_COMPLETED) {
            $currentProgress = '✅ Hoàn tất';
        } elseif ($lastTask && $lastTask->status === CrawlTask::STATUS_COMPLETED_WITH_ERRORS) {
            $currentProgress = '⚠️ Hoàn tất (có lỗi)';
        } elseif ($lastTask && $lastTask->status === CrawlTask::STATUS_FAILED) {
            $currentProgress = '❌ Thất bại';
        }

        return response()->json([
            'total_items' => $totalItems,
            'today_items' => $todayItems,
            'avg_duration' => $avgFormatted,
            'last_status' => $lastStatus,
            'last_time' => $lastTime,
            'running_jobs' => $runningJobs,
            'current_progress' => $currentProgress,
        ]);
    }
}
