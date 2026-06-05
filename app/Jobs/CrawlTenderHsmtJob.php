<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Services\CrawlTracker;
use App\Services\HsmtService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\CrawlLogger;

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
        $logger = app(CrawlLogger::class);

        try {

            $tender = Tender::find(
                $this->tenderId
            );

            if (!$tender) {
                $logger->warning($this->taskId, 'Hsmt job: tender not found', [
                    'tender_id' => $this->tenderId,
                ], 'hsmt');

                $tracker->jobFinished($this->taskId);

                return;
            }

            $service->handle($tender->id);

            // mark hsmt job as processed (atomic)
            $this->incrementProcessedItemsIfNeeded();

            $tracker->jobFinished($this->taskId);
        } catch (\Throwable $e) {
            $logger->error($this->taskId, 'HSMT job failed', [
                'tender_id' => $this->tenderId,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'exception' => $e,
            ], 'hsmt');

            throw $e;
        }
    }

    public function failed(
        \Throwable $e
    ): void {

        // count as processed and failed on permanent failure (atomic)
        $this->incrementProcessedAndFailedIfNeeded();

        app(CrawlTracker::class)->jobFinished($this->taskId);
        $logger = app(CrawlLogger::class);

        $logger->error($this->taskId, 'HSMT permanently failed', [
            'tender_id' => $this->tenderId,
            'attempts' => $this->attempts(),
            'error' => $e->getMessage(),
            'exception' => $e,
        ], 'hsmt');
    }

    private function incrementProcessedItemsIfNeeded(): void
    {
        DB::transaction(function () {
            $task = DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->lockForUpdate()
                ->first();

            if (!$task) {
                return;
            }

            $processed = (int) ($task->processed_items ?? 0);

            DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->update(['processed_items' => $processed + 1]);
        });
    }

    private function incrementProcessedAndFailedIfNeeded(): void
    {
        DB::transaction(function () {
            $task = DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->lockForUpdate()
                ->first();

            if (!$task) {
                return;
            }

            $processed = (int) ($task->processed_items ?? 0);
            $failed = (int) ($task->failed_items ?? 0);

            DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->update([
                    'processed_items' => $processed + 1,
                    'failed_items' => $failed + 1,
                ]);
        });
    }
}
