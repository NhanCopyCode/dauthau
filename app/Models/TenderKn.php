<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderKn extends Model
{
    protected $fillable = [
        'notify_no',

        'kn_count',
        'latest_req_date',
        'latest_res_date',
        'data',
        'raw',
    ];

    protected $casts = [
        'data' => 'array',
        'raw' => 'array',
        'latest_req_date' => 'datetime',
        'latest_res_date' => 'datetime',
    ];
}
