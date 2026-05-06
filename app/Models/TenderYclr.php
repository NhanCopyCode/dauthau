<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderYclr extends Model
{
    protected $fillable = [
        'notify_no',
        'notify_version',
        'data',
        'raw',
    ];

    protected $casts = [
        'data' => 'array',
        'raw' => 'array',
    ];
}
