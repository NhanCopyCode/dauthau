<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Services\CrawlTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CrawlTenderSubResourceJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 60;

    protected int $tenderId;
    protected string $type;
    protected int $taskId;
    public function __construct(
        int $tenderId,
        string $type,
        int $taskId
    ) {
        $this->tenderId = $tenderId;
        $this->type = $type;
        $this->taskId = $taskId;
    }
    public function handle()
    {
        $tracker = app(CrawlTracker::class);

        try {

            $tender = Tender::find($this->tenderId);

            if (!$tender || !$tender->notify_no) {

                Log::warning('Tender invalid', [
                    'tender_id' => $this->tenderId,
                    'type' => $this->type,
                ]);

                $tracker->jobFinished($this->taskId);

                return;
            }

            match ($this->type) {

                'yclr' => app(
                    \App\Services\YclrService::class
                )->handle($tender),

                'hntdt' => app(
                    \App\Services\HntdtService::class
                )->handle($tender),

                'kn' => app(
                    \App\Services\KnService::class
                )->handle($tender),

                default => null
            };

            // mark this subresource as processed (atomic)
            $this->incrementProcessedItemsIfNeeded();

            $tracker->jobFinished($this->taskId);
        } catch (\Throwable $e) {


            Log::error(
                "SubResource {$this->type} failed",
                [
                    'tender_id' => $this->tenderId,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    public function failed(
        \Throwable $e
    ): void {

        Log::critical(
            "SubResource {$this->type} permanently failed",
            [
                'task_id' => $this->taskId,
                'tender_id' => $this->tenderId,
                'attempts' => $this->attempts(),
                'error' => $e->getMessage(),
            ]
        );

        try {
            // count as processed and failed on permanent failure (atomic)
            $this->incrementProcessedAndFailedIfNeeded();
        } catch (\Throwable $ignored) {
            Log::error('SubResource failed() - incrementProcessedAndFailedIfNeeded failed', [
                'task_id' => $this->taskId,
                'error' => $ignored->getMessage(),
            ]);
        }

        try {
            app(CrawlTracker::class)->jobFinished($this->taskId);
        } catch (\Throwable $ignored) {
            Log::error('SubResource failed() - jobFinished failed', [
                'task_id' => $this->taskId,
                'error' => $ignored->getMessage(),
            ]);
        }
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
