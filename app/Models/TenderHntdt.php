<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderHntdt extends Model
{
    protected $fillable = [
        'notify_no',
        'notify_version',
        'data',
        'raw'
    ];

    protected $casts = [
        'raw' => 'array',
        'data' => 'array',
    ];
}