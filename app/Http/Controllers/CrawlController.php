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

            'status' => 'pending',
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

            'status' => 'pending',

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

            'status' => 'pending',

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

        $paginator = CrawlTask::query()
            ->latest('started_at')
            ->paginate($perPage);

        $items = collect($paginator->items())->map(function ($task) {
            return [
                'id' => $task->id,
                'type' => $task->type,
                'status' => $task->status,

                'from_date' => optional($task->from_date)->format('Y-m-d'),
                'to_date' => optional($task->to_date)->format('Y-m-d'),
                'started_at' => optional($task->started_at)->format('Y-m-d H:i:s'),

                'finished_at' => optional($task->finished_at)->format('Y-m-d H:i:s'),

                'processed_items' => $task->processed_items ?? 0,

                'total_items' => $task->total_items ?? 0,

                'progress' => (
                    ($task->processed_items ?? 0) . '/' . ($task->total_items ?? 0)
                ),

                'error' => $task->error,
            ];
        })->values();

        return response()->json([
            'tasks' => $items,
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

        /**
         * Tổng số gói thầu thực tế
         */
        $totalItems = Tender::query()
            ->count();

        /**
         * Gói thầu mới hôm nay
         */
        $todayItems = Tender::query()
            ->where('created_at', '>=', $today)
            ->count();

        /**
         * Average crawl duration
         */
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

        /**
         * Task gần nhất
         */
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

        /**
         * Running task
         */
        $runningJobs = CrawlTask::query()
            ->where('status', 'running')
            ->count();

        $runningTask = CrawlTask::query()
            ->where('status', 'running')
            ->latest('started_at')
            ->first();

        $currentProgress = '--';

        if ($runningTask) {
            $processed = $runningTask->processed_items ?? 0;
            $total = $runningTask->total_items ?? 0;
            $currentProgress = "{$processed}/{$total}";
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
