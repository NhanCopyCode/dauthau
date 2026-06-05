<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CrawlTask extends Model
{
    public const TYPE_FULL = 'full';
    public const TYPE_DAILY = 'daily';
    public const TYPE_RANGE = 'range';

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';

    protected $fillable = [
        'type',
        'status',
        'from_date',
        'to_date',
        'total_pages',
        'processed_pages',
        'total_items',
        'api_total_items',
        'processed_items',
        'failed_items',
        'started_at',
        'finished_at',
        'error',
    ];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'api_total_items' => 'integer',
        'failed_items' => 'integer',
    ];

    public function getDurationAttribute(): string
    {
        if (!$this->started_at || !$this->finished_at) {
            return '--';
        }

        $seconds = Carbon::parse($this->started_at)
            ->diffInSeconds(Carbon::parse($this->finished_at));

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return "{$minutes}m {$remainingSeconds}s";
    }

    public function logs()
    {
        return $this->hasMany(CrawlLog::class, 'crawl_task_id')->orderBy('created_at', 'asc');
    }
}
