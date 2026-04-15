<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tender;

class CleanupTenderCommand extends Command
{
    protected $signature = 'tenders:cleanup';
    protected $description = 'Cleanup old tenders';

    public function handle()
    {
        $this->info("Start cleanup...");

        $inactive = Tender::where('last_seen_at', '<', now()->subDay())
            ->where('is_active', 1)
            ->update(['is_active' => 0]);

        $this->info("Marked inactive: {$inactive}");

        $deleted = Tender::where('is_active', 0)
            ->where('updated_at', '<', now()->subDay())
            ->delete();

        $this->info("Deleted: {$deleted}");

        return Command::SUCCESS;
    }
}
