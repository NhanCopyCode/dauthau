<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Services\TenderDetailCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CrawlTenderDetailJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $timeout = 60;
    public $backoff = [10, 30, 60];

    protected $tenderId;

    public function __construct($tenderId)
    {
        $this->tenderId = $tenderId;
    }

    public function handle(TenderDetailCrawlerService $service)
    {
        $tender = Tender::find($this->tenderId);

        if (!$tender) {
            Log::warning("Tender not found: {$this->tenderId}");
            return;
        }

        try {
            $service->handle($tender);
        } catch (\Exception $e) {
            Log::error("Detail crawl failed: {$tender->id} - " . $e->getMessage());
            throw $e;
        }
    }
}
