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

    public function detail()
    {
        return $this->hasOne(TenderDetail::class);
    }

    public function getInvestFieldLabelAttribute()
    {
        return match ($this->invest_field) {
            'XL' => 'Xây lắp',
            'HH' => 'Hàng hóa',
            'PTV' => 'Phi tư vấn',
            'HON_HOP' => 'Hỗn hợp',
            'TV' => 'Tư vấn',
            default => '—',
        };
    }

    public function getLocationFullAttribute(): array
    {
        if (empty($this->locations) || !is_array($this->locations)) {
            return [];
        }

        return collect($this->locations)
            ->map(function ($location) {
                $district = $location['districtName'] ?? null;
                $province = $location['provName'] ?? null;

                return collect([$district, $province])
                    ->filter()
                    ->implode(', ');
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
    public function getPublicDateLabelAttribute(): ?string
    {
        if (!$this->public_date) {
            return null;
        }

        return $this->public_date->format('d/m/Y H:i');
    }

    public function hsmtChapters()
    {
        return $this->hasMany(\App\Models\TenderHsmtChapter::class, 'tender_id')
            ->orderBy('order_index');
    }

    public function getHasHsmtAttribute(): bool
    {
        if ($this->relationLoaded('hsmtChapters')) {
            return $this->hsmtChapters->isNotEmpty();
        }

        return $this->hsmtChapters()->exists();
    }
}
