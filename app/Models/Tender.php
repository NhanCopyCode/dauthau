<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Tender extends Model
{
    protected $table = 'tenders';

    protected $fillable = [
        'egp_id',
        'notify_id',
        'bid_id',
        'notify_no',
        'notify_version',
        'notify_no_stand',
        'name',
        'bid_names',
        'investor',
        'investor_code',
        'province',
        'locations',
        'bid_close_date',
        'bid_open_date',
        'public_date',
        'original_public_date',
        'plan_no',
        'plan_type',
        'bid_form',
        'bid_mode',
        'process_apply',
        'invest_field',
        'invest_fields',
        'bid_price',
        'status',
        'status_for_notify',
        'type',
        'step_code',
        'num_petition',
        'num_clarify_req',
        'num_bidder_tech',
        'num_petition_hsmt',
        'num_petition_lcnt',
        'num_petition_kqlcnt',
        'is_internet',
        'is_domestic',
        'is_medicine',
        'created_by',
        'score',
    ];

    protected $casts = [
        'bid_names' => 'array',
        'locations' => 'array',
        'invest_fields' => 'array',
        'bid_close_date' => 'datetime',
        'bid_open_date' => 'datetime',
        'public_date' => 'datetime',
        'original_public_date' => 'datetime',
        'bid_price' => 'decimal:2',
        'is_internet' => 'boolean',
        'is_domestic' => 'boolean',
        'is_medicine' => 'boolean',
    ];

    /**
     * Scope: gói thầu chưa đóng
     */
    public function scopeOpening(Builder $query)
    {
        return $query->whereNotNull('bid_close_date')
            ->where('bid_close_date', '>', now());
    }
}
