<?php

namespace App\Helpers;

class TenderHelper
{
    public static function formatBidMode(array $item): string
    {
        $bidForm = $item['bidForm'] ?? null;
        $bidMode = $item['bidMode'] ?? null;

        $noBidModeForms = [
            'CGTTRG',
        ];

        if (in_array($bidForm, $noBidModeForms, true)) {
            return 'Không có phương thức LCNT';
        }

        $mapping = [
            '1_MTHS' => 'Một giai đoạn một túi hồ sơ',
            '1_HTHS' => 'Một giai đoạn hai túi hồ sơ',
            '2_GD'   => 'Hai giai đoạn',
            '2_THS'  => 'Hai giai đoạn hai túi hồ sơ',
        ];

        return $mapping[$bidMode] ?? '—';
    }
}
