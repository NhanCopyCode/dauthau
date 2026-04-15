<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderDetail extends Model
{
    protected $table = 'tender_details';

    public const BID_MODE_MAP = [
        '1_MTHS' => '1 giai đoạn 1 túi hồ sơ',
        '1_HTHS' => '1 giai đoạn 2 túi hồ sơ',
    ];
    public const PLAN_TYPE_MAP = [
        'TX'   => 'Chi thường xuyên',
        'DTPT' => 'Đầu tư phát triển',
        'DTMS' => 'Mua sắm thường xuyên',
        'KHAC' => 'Khác',
    ];

    public const BID_FORM_MAP = [
        'CGTTRG' => 'Chào giá trực tuyến rút gọn',
        'CHCT'   => 'Chào hàng cạnh tranh',
        'CHCTRG' => 'Chào hàng cạnh tranh rút gọn',
        'DTHC'   => 'Đấu thầu hạn chế',
        'DTRR'   => 'Đấu thầu rộng rãi',
        'KHAC'   => 'Khác',
        'TVCN'   => 'Tư vấn cá nhân',
    ];
    protected $guarded = [];


    protected $casts = [
        'publish_date' => 'datetime',
        'bid_close_date' => 'datetime',
        'bid_open_date' => 'datetime',
        'approval_date' => 'datetime',

        'ehsdt_fee' => 'decimal:2',
        'bid_guarantee_amount' => 'decimal:2',

        'is_domestic' => 'boolean',
        'is_multi_lot' => 'boolean',

        'reoffer_start_time' => 'datetime',
        'reoffer_end_time' => 'datetime',

        'lot_table' => 'array',
        'scope_table' => 'array',
        'raw_json' => 'array',
    ];



    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }


    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('publish_date');
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('publish_date', '>=', now()->subDays($days));
    }

    public function scopeDomestic(Builder $query): Builder
    {
        return $query->where('is_domestic', true);
    }

    public function scopeHasBidClosing(Builder $query): Builder
    {
        return $query->whereNotNull('bid_close_time');
    }


    public function getIsExpiredAttribute(): bool
    {
        if (!$this->bid_close_time) {
            return false;
        }

        return $this->bid_close_time->isPast();
    }

    public function getBidStatusAttribute(): string
    {
        if (!$this->bid_close_time) {
            return 'unknown';
        }

        return $this->bid_close_time->isPast() ? 'closed' : 'opening';
    }



    public function isOpen(): bool
    {
        return !$this->is_expired;
    }

    public function isClosed(): bool
    {
        return $this->is_expired;
    }

    public function getContractTypeLabelAttribute(): string
    {
        return match ($this->contract_type) {
            'DGCD' => 'Đơn giá cố định',
            'DGCD_DC' => 'Đơn giá cố định (điều chỉnh)',
            'DGDC' => 'Đơn giá điều chỉnh',
            'TG' => 'Trọn gói',
            'TG_DGCD' => 'Trọn gói + Đơn giá cố định',
            'TG_DGDC' => 'Trọn gói + Đơn giá điều chỉnh',
            'TLPT' => 'Theo tỷ lệ phần trăm',
            default => $this->contract_type ?? '—',
        };
    }

    public function getBidModeLabelAttribute(): string
    {
        return self::BID_MODE_MAP[$this->bid_mode]
            ?? $this->bid_mode
            ?? '—';
    }

    public function getBidFormLabelAttribute(): string
    {
        return self::BID_FORM_MAP[$this->bid_form]
            ?? $this->bid_form
            ?? '—';
    }

    public function getContractPeriodLabelAttribute(): string
    {
        if (!$this->contract_period) {
            return '—';
        }

        return match ($this->contract_period_unit) {
            'D' => $this->contract_period . ' ngày',
            'M' => $this->contract_period . ' tháng',
            'Y' => $this->contract_period . ' năm',
            default => $this->contract_period,
        };
    }

    public function getIsReofferAttribute(): bool
    {
        return str_contains($this->tender?->step_code ?? '', 'reoffer');
    }

    public function getBidValidityPeriodLabelAttribute(): string
    {
        if (!$this->bid_validity_period) {
            return '—';
        }

        return match ($this->bid_validity_period_unit) {
            'D' => $this->bid_validity_period . ' ngày',
            'M' => $this->bid_validity_period . ' tháng',
            'Y' => $this->bid_validity_period . ' năm',
            default => $this->bid_validity_period . ' ngày',
        };
    }

    public function getBidParticipationFormAttribute(): string
    {
        $text = match ($this->is_online_bidding) {
            1 => 'Qua mạng',
            0 => 'Không qua mạng',
            default => '-',
        };

        if (
            $this->is_multi_lot == 1 &&
            $this->is_domestic != 0
        ) {
            $text .= ', Nhà thầu tham dự 1 hoặc nhiều phần (lô)';
        }

        return $text;
    }

    public function getLotTableColumnsAttribute()
    {
        return $this->lot_table['columns'] ?? [];
    }

    public function getLotTableRowsAttribute()
    {
        return $this->lot_table['rows'] ?? [];
    }

    public function getScopeTableColumnsAttribute()
    {
        return $this->scope_table['columns'] ?? [];
    }

    public function getScopeTableRowsAttribute()
    {
        return $this->scope_table['rows'] ?? [];
    }

    public function formatScopeValue(string $key, $value): string
    {
        if (is_null($value)) {
            return '—';
        }

        // if (is_numeric($value)) {
        //     return number_format($value, 0, ',', '.');
        // }

        return (string) $value;
    }

    public function getScopeColumnWidth($key)
    {
        return match ($key) {
            'pos' => '70px',

            'lotNo' => '150px',
            'lotName' => '150px',

            'name' => '300px',

            'uom' => '100px',
            'qty' => '100px',

            'code' => '100px',
            'brand' => '100px',

            'manufacture' => '100px',

            'goodsOrigin' => '100px',

            'manufactureYear' => '100px',

            'specification' => '500px',

            'projectPlace' => '100px',

            'earlietDeliveryDate' => '100px',
            'lateDeliveryDate' => '100px',

            'otherRequirement' => '200px',

            default => '150px'
        };
    }

    public function getPlanTypeLabelAttribute(): string
    {
        return self::PLAN_TYPE_MAP[$this->plan_type] ?? '—';
    }

    public function getBidGuaranteeDisplayAttribute(): ?string
    {
        if (!$this->bid_guarantee_amount) {
            return null;
        }

        return number_format($this->bid_guarantee_amount, 0, ',', '.') . ' VND';
    }

    public function getShowBidGuaranteePolicyAttribute(): bool
    {
        return $this->tender->process_apply === 'LDT'
            && $this->isPolicyVer2($this->public_date);
    }

    public function getBidGuaranteePolicyTextAttribute(): ?string
    {
        $version = $this->getVersionND($this->public_date);

        return match ($version) {
            'ND2024' => '(Theo Điều 18 Nghị định 24/2024/NĐ-CP, nhà thầu có tên trong danh sách không bảo đảm uy tín khi tham dự thầu, khi tham dự thầu phải thực hiện biện pháp bảo đảm dự thầu với giá trị gấp 03 lần giá trị yêu cầu đối với nhà thầu khác trong thời hạn 02 năm kể từ lần cuối cùng thực hiện các hành vi quy định tại khoản 1 Điều này.).',
            'ND2025' => '(Theo Điều 20 Nghị định 214/2025/NĐ-CP, nhà thầu có tên trong danh sách không bảo đảm uy tín khi tham dự thầu, khi tham dự thầu phải thực hiện biện pháp bảo đảm dự thầu với giá trị gấp 03 lần giá trị yêu cầu đối với nhà thầu khác trong thời hạn 02 năm kể từ lần cuối cùng thực hiện các hành vi quy định tại khoản 1 Điều này.).',
            default => null,
        };
    }
    public function isPolicyVer2($date): bool
    {
        if (!$date) {
            return false;
        }

        return \Carbon\Carbon::parse($date)->gte('2024-01-01');
    }

    public function getVersionND($date): ?string
    {
        if (!$date) {
            return null;
        }

        $date = \Carbon\Carbon::parse($date);

        if ($date->year == 2024) {
            return 'ND2024';
        }

        if ($date->year >= 2025) {
            return 'ND2025';
        }

        return null;
    }


    public function getIsMultiLotLabelAttribute(): string
    {
        return (int) $this->is_multi_lot === 1 ? 'Có' : 'Không';
    }

    public function getEffectiveCloseTimeAttribute()
    {
        return $this->bid_close_date ?? $this->reoffer_end_time;
    }

    public function getEffectiveCloseTimeFormattedAttribute(): ?string
    {
        return $this->effective_close_time
            ? \Carbon\Carbon::parse($this->effective_close_time)->format('H:i')
            : null;
    }

    public function getEffectiveCloseDateFormattedAttribute(): ?string
    {
        return $this->effective_close_time
            ? \Carbon\Carbon::parse($this->effective_close_time)->format('d/m/Y')
            : null;
    }

    public function getEffectiveCountdownAttribute(): ?string
    {
        if (!$this->effective_close_time) {
            return null;
        }

        $time = \Carbon\Carbon::parse($this->effective_close_time);

        if ($time->isPast()) {
            return 'Đã đóng thầu';
        }

        $diff = now()->diff($time);

        return sprintf(
            '%d ngày %d giờ %d phút',
            $diff->d + ($diff->m * 30) + ($diff->y * 365),
            $diff->h,
            $diff->i
        );
    }

    public function getScopeTitleAttribute(): string
    {
        if ($this->bid_form === 'CGTT') {
            return 'Phạm vi cung cấp';
        }

        if ($this->bid_form === 'CGTTRG') {
            return $this->invest_field === 'XL'
                ? 'Phạm vi công việc'
                : 'Phạm vi cung cấp';
        }

        return 'Phạm vi cung cấp';
    }

    public function getCeilingPriceDisplayAttribute(): ?string
    {
        if (!$this->ceiling_price) return null;

        return number_format($this->ceiling_price, 0, ',', '.') . ' VND';
    }

    public function getPriceStepDisplayAttribute(): ?string
    {
        if (!$this->price_step) return null;

        return number_format($this->price_step, 0, ',', '.') . ' VND';
    }
}
