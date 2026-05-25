<?php

namespace App\Jobs;

use App\Models\CrawlTask;
use App\Services\CrawlTracker;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RunCrawlTaskJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $taskId
    ) {}

    public function handle(): void
    {
        $task = CrawlTask::findOrFail(
            $this->taskId
        );

        app(CrawlTracker::class)->start($task->id);


        $task->update([

            'status' => 'running',

            'started_at' => now(),

            'finished_at' => null,

            'processed_pages' => 0,

            'processed_items' => 0,

            'total_pages' => 0,

            'total_items' => 0,

            'error' => null,
        ]);
        switch ($task->type) {

            case 'full':

                $this->runFull($task);

                break;

            case 'range':

                $this->runRange($task);

                break;

            case 'daily':

                $this->runDaily($task);

                break;
        }
    }

    private function runFull(CrawlTask $task): void
    {

        $tracker = app(CrawlTracker::class);

        dispatch(
            new CrawlTenderJob(
                page: 0,
                taskId: $task->id
            )
        )->onQueue('crawl');
        $tracker->jobDispatched($task->id);
    }

    private function runDaily(CrawlTask $task): void
    {
        $tracker = app(CrawlTracker::class);

        dispatch(
            new CrawlTenderDateJob(
                date: now()->toDateString(),
                page: 0,
                taskId: $task->id
            )
        )->onQueue('crawl');

        $tracker->jobDispatched($task->id);
        // $tracker->markProducerDone($task->id);
    }

    // private function runRange(
    //     CrawlTask $task
    // ): void {

    //     $tracker = app(
    //         CrawlTracker::class
    //     );

    //     $current = $task
    //         ->from_date
    //         ->copy()
    //         ->startOfDay();

    //     $endDate = $task
    //         ->to_date
    //         ->copy()
    //         ->startOfDay();

    //     while (
    //         $current <= $endDate
    //     ) {

    //         dispatch(
    //             new CrawlTenderDateJob(
    //                 date: $current
    //                     ->toDateString(),
    //                 page: 0,
    //                 taskId: $task->id
    //             )
    //         )->onQueue('crawl');

    //         $tracker->jobDispatched($task->id);

    //         $current->addDay();
    //     }

    //     // $tracker->markProducerDone($task->id);
    // }

    private function runRange(
        CrawlTask $task
    ): void {

        $tracker = app(
            CrawlTracker::class
        );

        $startDate = $task
            ->from_date
            ->copy()
            ->startOfDay();

        $endDate = $task
            ->to_date
            ->copy()
            ->startOfDay();

        $dates = [];

        while (
            $startDate->lte($endDate)
        ) {

            $dates[] = $startDate
                ->copy();

            $startDate->addDay();
        }

        Log::info(
            'RANGE PRODUCER START',
            [
                'task_id' => $task->id,
                'from' => $task->from_date
                    ->toDateString(),
                'to' => $task->to_date
                    ->toDateString(),
                'days' => count($dates),
            ]
        );

        foreach ($dates as $date) {

            dispatch(
                new CrawlTenderDateJob(
                    date: $date
                        ->toDateString(),
                    page: 0,
                    taskId: $task->id
                )
            )->onQueue('crawl');

            $tracker->jobDispatched(
                $task->id
            );

            Log::info(
                'DATE JOB DISPATCHED',
                [
                    'task_id' => $task->id,
                    'date' => $date
                        ->toDateString(),
                ]
            );
        }

        // VERY IMPORTANT
        $tracker->markProducerDone(
            $task->id
        );

        Log::info(
            'RANGE PRODUCER DONE',
            [
                'task_id' => $task->id,
                'jobs_dispatched' => count(
                    $dates
                ),
            ]
        );
    }
}
