<?php

namespace App\Http\Controllers;

use App\Jobs\RunCrawlTaskJob;
use App\Models\CrawlTask;
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
        $tasks = CrawlTask::query()
            ->latest('started_at')
            ->limit(10)
            ->get()
            ->map(function ($task) {

                return [
                    'id' => $task->id,
                    'type' => $task->type,
                    'status' => $task->status,

                    'from_date' => optional($task->from_date)->format('Y-m-d'),
                    'to_date' => optional($task->to_date)->format('Y-m-d'),
                    'started_at' => optional(
                        $task->started_at
                    )->format('Y-m-d H:i:s'),

                    'finished_at' => optional(
                        $task->finished_at
                    )->format('Y-m-d H:i:s'),

                    'processed_items' =>
                    $task->processed_items ?? 0,

                    'total_items' =>
                    $task->total_items ?? 0,

                    'error' => $task->error,
                ];
            });

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    public function stats()
    {
        // Tổng số item dự kiến từ API (total_items của tất cả task)
        $totalItems = (int) CrawlTask::query()->sum('total_items');

        // Lấy múi giờ Việt Nam
        $now = now()->setTimezone('Asia/Ho_Chi_Minh');
        $today = $now->toDateString();

        // Tổng số item dự kiến của các task được bắt đầu vào hôm nay
        $todayItems = (int) CrawlTask::whereDate('started_at', $today)->sum('total_items');

        // Các task đã hoàn thành (có finished_at)
        $completed = CrawlTask::whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->get();

        $durations = $completed->map(function ($task) {
            return Carbon::parse($task->started_at)->diffInSeconds(Carbon::parse($task->finished_at));
        });

        $avgSeconds = $durations->count() ? (int) round($durations->avg()) : 0;
        $avgFormatted = $avgSeconds ? floor($avgSeconds / 60) . 'm ' . ($avgSeconds % 60) . 's' : '--';

        // Task gần nhất
        $last = CrawlTask::orderByDesc('started_at')->first();

        $lastStatus = $last ? $last->status : '--';
        $lastTimeFormatted = '--';
        if ($last && $last->started_at) {
            $lastTimeFormatted = Carbon::parse($last->started_at)
                ->setTimezone('Asia/Ho_Chi_Minh')
                ->format('H:i d/m/Y');
        }

        $running = CrawlTask::where('status', 'running')->count();

        return response()->json([
            'total_items' => $totalItems,
            'today_items' => $todayItems,
            'avg_duration' => $avgFormatted,
            'last_status' => $lastStatus,
            'last_time' => $lastTimeFormatted,
            'running_jobs' => $running,
        ]);
    }
}
