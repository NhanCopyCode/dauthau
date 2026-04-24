<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorResult extends Model
{
    protected $fillable = [
        'tender_id',
        'contractor_code',
        'contractor_name',
        'reoffer_price',
        'reoffer_price_final',
        'times',
        'lot_no',
        'lot_name',
        'reoffer_date',
        'form_value',
        'is_newest'
    ];

    protected $casts = [
        'form_value' => 'array',
        'reoffer_date' => 'datetime',
    ];
}
