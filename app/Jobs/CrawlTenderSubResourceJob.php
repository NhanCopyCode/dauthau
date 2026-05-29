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

            // mark this subresource as processed
            DB::table('crawl_tasks')->where('id', $this->taskId)
                ->update(['processed_items' => DB::raw('processed_items + 1')]);

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

        // ensure we count the subresource as finished even when it failed permanently
        DB::table('crawl_tasks')->where('id', $this->taskId)
            ->update(['processed_items' => DB::raw('processed_items + 1')]);

        app(CrawlTracker::class)->jobFinished($this->taskId);

        Log::critical(
            "SubResource {$this->type} permanently failed",
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
