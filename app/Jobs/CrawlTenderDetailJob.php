<?php

namespace App\Jobs;

use App\Exceptions\TemporaryCrawlerException;
use App\Models\Tender;
use App\Services\CrawlTracker;
use App\Services\TenderDetailCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\CrawlLogger;
use Throwable;

class CrawlTenderDetailJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $timeout = 60;

    public $backoff = [10, 30, 60];

    protected int $tenderId;

    protected int $taskId;

    public function __construct(
        int $tenderId,
        int $taskId
    ) {
        $this->tenderId = $tenderId;
        $this->taskId = $taskId;
    }

    public function backoff(): array
    {
        return [
            30,
            120,
            300,
            900,
        ];
    }

    // public function handle(
    //     TenderDetailCrawlerService $service
    // ): void {
    //     $tracker = app(CrawlTracker::class);
    //     try {
    //         $tender = Tender::find($this->tenderId);
    //         if (!$tender) {

    //             Log::warning('Tender not found', [

    //                 'tender_id' => $this->tenderId,
    //             ]);

    //             $tracker->jobFinished(
    //                 $this->taskId
    //             );


    //             return;
    //         }
    //         $tenderDetail =
    //             $service->handle(
    //                 $tender
    //             );

    //         /**
    //          * Dispatch HSMT
    //          */
    //         if ($tenderDetail) {

    //             $tracker->jobDispatched(
    //                 $this->taskId
    //             );

    //             dispatch(
    //                 new CrawlTenderHsmtJob(
    //                     tenderId: $tenderDetail
    //                         ->tender_id,

    //                     taskId: $this->taskId
    //                 )
    //             )->onQueue('hsmt');
    //         }

    //         $tracker->jobFinished($this->taskId);
    //     } catch (\Throwable $e) {

    //         Log::error('Detail crawl attempt failed', [

    //             'tender_id' => $this->tenderId,

    //             'attempt' => $this->attempts(),

    //             'max_tries' => $this->tries,

    //             'error' => $e->getMessage(),
    //         ]);

    //         throw $e;
    //     }
    // }


    // public function failed(
    //     \Throwable $e
    // ): void {

    //     app(CrawlTracker::class)
    //         ->jobFinished(
    //             $this->taskId
    //         );

    //     Log::critical(
    //         'Detail crawl permanently failed',
    //         [

    //             'task_id' =>
    //             $this->taskId,

    //             'tender_id' =>
    //             $this->tenderId,

    //             'attempts' =>
    //             $this->attempts(),

    //             'error' =>
    //             $e->getMessage(),
    //         ]
    //     );
    // }

    public function handle(
        TenderDetailCrawlerService $service
    ): void {

        $tracker = app(CrawlTracker::class);
        $logger = app(CrawlLogger::class);

        $tender = Tender::find(
            $this->tenderId
        );

        if (!$tender) {
            $logger->warning($this->taskId, 'Tender not found', [
                'tender_id' => $this->tenderId,
            ], 'detail');

            $tracker->jobFinished($this->taskId);

            return;
        }

        try {

            $logger->info($this->taskId, 'DETAIL CRAWL START', [
                'tender_id' => $tender->id,
                'attempt' => $this->attempts(),
            ], 'detail');

            $tenderDetail =
                $service->handle(
                    $tender
                );

            /**
             * Dispatch HSMT
             */
            if ($tenderDetail) {

                dispatch(
                    new CrawlTenderHsmtJob(
                        tenderId: $tenderDetail
                            ->tender_id,
                        taskId: $this->taskId
                    )
                )->onQueue('hsmt');

                $tracker
                    ->jobDispatched(
                        $this->taskId
                    );
            }

            $logger->info($this->taskId, 'DETAIL SUCCESS', [
                'tender_id' => $tender->id,
            ], 'detail');

            // mark this detail job as processed in the task counters
            DB::table('crawl_tasks')->where('id', $this->taskId)
                ->update(['processed_items' => DB::raw('processed_items + 1')]);

            $tracker->jobFinished($this->taskId);
        } catch (
            TemporaryCrawlerException $e
        ) {

            $logger->warning($this->taskId, 'TEMP DETAIL FAILURE', [
                'tender_id' => $tender->id,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'error' => $e->getMessage(),
                'exception' => $e,
            ], 'detail');

            throw $e;
        } catch (\Throwable $e) {

            $logger->error($this->taskId, 'PERMANENT DETAIL FAILURE', [
                'tender_id' => $tender->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'exception' => $e,
            ], 'detail');

            throw $e;
        }
    }

    public function failed(
        Throwable $e
    ): void {
        $logger = app(CrawlLogger::class);
        // ensure this job counts as processed even on permanent failure
        DB::table('crawl_tasks')->where('id', $this->taskId)
            ->update(['processed_items' => DB::raw('processed_items + 1')]);

        $logger->error($this->taskId, 'DETAIL JOB FAILED PERMANENTLY', [
            'tender_id' => $this->tenderId,
            'error' => $e->getMessage(),
            'exception' => $e,
        ], 'detail');
    }
}
