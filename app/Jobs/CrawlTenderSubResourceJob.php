<?php

namespace App\Jobs;

use App\Models\Tender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CrawlTenderSubResourceJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 60;

    protected int $tenderId;
    protected string $type;

    public function __construct(int $tenderId, string $type)
    {
        $this->tenderId = $tenderId;
        $this->type = $type;
    }

    public function handle()
    {
        $tender = Tender::find($this->tenderId);

        if (!$tender || !$tender->notify_no) {
            return;
        }

        try {
            match ($this->type) {
                'yclr' => app(\App\Services\YclrService::class)
                    ->handle($tender),

                'hntdt' => app(\App\Services\HntdtService::class)
                    ->handle($tender),

                'kn' => app(\App\Services\KnService::class)
                    ->handle($tender),

                default => null
            };
        } catch (\Throwable $e) {
            Log::error("SubResource {$this->type} failed", [
                'tender_id' => $this->tenderId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
