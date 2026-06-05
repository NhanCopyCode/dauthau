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

        // if (app()->environment('local')) {
        //     return [1, 1];
        // }

        return [
            30,
            120,
            300,
            900,
        ];
    }


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

            // if (
            //     app()->environment('local')
            //     && $tender->id % 10 === 0
            // ) {
            //     throw new TemporaryCrawlerException(
            //         'cURL error 28: Operation timed out'
            //     );
            // }

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

            // mark this detail job as processed in the task counters (atomic)
            $this->incrementProcessedItemsIfNeeded();

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
        // ensure this job counts as processed and recorded as failed (atomic)
        $this->incrementProcessedAndFailedIfNeeded();

        app(CrawlTracker::class)
            ->jobFinished(
                $this->taskId
            );

        $logger->error($this->taskId, 'DETAIL JOB FAILED PERMANENTLY', [
            'tender_id' => $this->tenderId,
            'error' => $e->getMessage(),
            'exception' => $e,
        ], 'detail');
    }

    private function incrementProcessedAndFailedIfNeeded(): void
    {
        // increment both processed_items and failed_items atomically
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

    private function incrementProcessedItemsIfNeeded(): void
    {
        // use transaction + row lock to avoid race conditions between workers
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
}
