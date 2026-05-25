<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Services\CrawlTracker;
use App\Services\HsmtService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

// class CrawlTenderHsmtJob implements ShouldQueue
// {
//     use Queueable;

//     public int $tries = 3;
//     public int $timeout = 60;
//     public array $backoff = [10, 30, 60];

//     public function __construct(
//         protected int $tenderId
//     ) {}

//     public function handle(HsmtService $service): void
//     {

//         $tracker = app(CrawlTracker::class);

//         try {
//             $tender = Tender::find($this->tenderId);

//             if (!$tender) {
//                 Log::warning('Hsmt job: tender not found', [
//                     'tender_id' => $this->tenderId,
//                 ]);

//                 $tracker->jobFinished($task->id);

//                 return;
//             }



//             $service->handle($tender->id);

//             $tracker->jobFinished($task->id);
//         } catch (\Throwable $e) {

//             Log::error('HSMT job failed', [
//                 'tender_id' => $this->tenderId,
//                 'error' => $e->getMessage(),
//             ]);

//             throw $e;
//         }
//     }

//     public function failed(\Throwable $e): void
//     {
//         app(CrawlTracker::class)
//             ->jobFinished();


//         Log::critical('HSMT permanently failed', [
//             'tender_id' => $this->tenderId,
//             'attempts' => $this->attempts(),
//             'error' => $e->getMessage(),
//         ]);
//     }
// }

class CrawlTenderHsmtJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [
        10,
        30,
        60
    ];

    public function __construct(
        protected int $tenderId,
        protected int $taskId
    ) {}

    public function handle(
        HsmtService $service
    ): void {

        $tracker = app(
            CrawlTracker::class
        );

        try {

            $tender = Tender::find(
                $this->tenderId
            );

            if (!$tender) {

                Log::warning(
                    'Hsmt job: tender not found',
                    [

                        'task_id' =>
                        $this->taskId,

                        'tender_id' =>
                        $this->tenderId,
                    ]
                );

                $tracker->jobFinished(
                    $this->taskId
                );

                return;
            }

            $service->handle(
                $tender->id
            );

            $tracker->jobFinished(
                $this->taskId
            );
        } catch (\Throwable $e) {

            Log::error(
                'HSMT job failed',
                [

                    'task_id' =>
                    $this->taskId,

                    'tender_id' =>
                    $this->tenderId,

                    'attempt' =>
                    $this->attempts(),

                    'error' =>
                    $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    public function failed(
        \Throwable $e
    ): void {

        app(CrawlTracker::class)
            ->jobFinished(
                $this->taskId
            );

        Log::critical(
            'HSMT permanently failed',
            [

                'task_id' =>
                $this->taskId,

                'tender_id' =>
                $this->tenderId,

                'attempts' =>
                $this->attempts(),

                'error' =>
                $e->getMessage(),
            ]
        );
    }
}