<?php

namespace App\Services;

use App\Models\Tender;
use Illuminate\Support\Facades\Log;

class TenderDetailResolverService
{
    public function buildStrategy(
        Tender $tender
    ): array {

        $stepCode = strtolower(
            trim(
                $tender->step_code ?? ''
            )
        );

        $processApply = strtolower(
            trim(
                $tender->process_apply ?? ''
            )
        );

        /**
         * Reoffer ưu tiên
         */
        if (
            str_contains(
                $stepCode,
                'reoffer'
            )
        ) {

            return [
                'reoffer',
                'adb',
                'ldt',
            ];
        }

        /**
         * ADB / WB / KHAC
         */
        if (
            in_array(
                $processApply,
                [
                    'adb',
                    'wb',
                    'khac',
                ]
            )
        ) {

            return [
                'adb',
                'ldt',
                'reoffer',
            ];
        }

        /**
         * LDT
         */
        if (
            $processApply
            === 'ldt'
        ) {

            return [
                'ldt',
                'adb',
                'reoffer',
            ];
        }

        /**
         * Unknown
         */
        Log::warning(
            'Unknown process_apply strategy',
            [
                'tender_id'
                => $tender->id,
                'process_apply'
                => $processApply,
                'step_code'
                => $stepCode,
            ]
        );

        return [
            'adb',
            'ldt',
            'reoffer',
        ];
    }

  
}
