<?php

namespace App\Jobs;

use App\Exceptions\TemporaryCrawlerException;
use App\Models\Tender;
use App\Services\CrawlTracker;
use App\Services\TenderDetailCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
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

        $tender = Tender::find(
            $this->tenderId
        );

        if (!$tender) {

            Log::warning(
                'Tender not found',
                [
                    'tender_id' =>
                    $this->tenderId,
                ]
            );

            $tracker->jobFinished(
                $this->taskId
            );

            return;
        }

        try {

            Log::info(
                'DETAIL CRAWL START',
                [
                    'tender_id' =>
                    $tender->id,

                    'attempt' =>
                    $this->attempts(),
                ]
            );

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

            Log::info(
                'DETAIL SUCCESS',
                [
                    'tender_id' =>
                    $tender->id,
                ]
            );

            $tracker->jobFinished(
                $this->taskId
            );
        } catch (
            TemporaryCrawlerException $e
        ) {

            Log::warning(
                'TEMP DETAIL FAILURE',
                [
                    'tender_id' =>
                    $tender->id,

                    'attempt' =>
                    $this->attempts(),

                    'max_tries' =>
                    $this->tries,

                    'error' =>
                    $e->getMessage(),
                ]
            );

            throw $e;
        } catch (\Throwable $e) {

            Log::error(
                'PERMANENT DETAIL FAILURE',
                [
                    'tender_id' =>
                    $tender->id,

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
        Throwable $e
    ): void {

        Log::error(
            'DETAIL JOB FAILED PERMANENTLY',
            [
                'tender_id' =>
                $this->tenderId,

                'error' =>
                $e->getMessage(),
            ]
        );
    }
}
