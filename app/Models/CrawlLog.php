<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'crawl_task_id',
        'queue',
        'level',
        'message',
        'context',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(CrawlTask::class, 'crawl_task_id');
    }
}
