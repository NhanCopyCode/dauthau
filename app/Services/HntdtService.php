<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderHntdt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HntdtService
{
    public function handle(Tender $tender): void
    {
        $url = config('crawler.hntdt_url') . '?token=' . config('crawler.token');

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withOptions(['verify' => false])
            ->post($url, [
                'notifyNo' => $tender->notify_no,
                'processApply' => $tender->process_apply ?? 'LDT'
            ]);

        if (!$response->successful()) {
            Log::error('HNTDT API failed', [
                'tender_id' => $tender->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return;
        }

        $versions = data_get($response->json(), 'bidoPreBidConferenceVersionDTOList', []);

        foreach ($versions as $version) {

            $items = data_get($version, 'bidoPreBidConferenceList', []);

            $rows = collect($items)->map(function ($item, $index) {

                return [
                    'stt' => $index + 1,

                    'content' => $item['content'] ?? null,
                    'content_file_name' => $item['contentFileName'] ?? null,
                    'content_file_id' => $item['contentFileId'] ?? null,
                    'content_date' => $item['contentPublicDate'] ?? null,

                    'report' => $item['reportInfo'] ?? null,
                    'report_file_name' => $item['reportFileName'] ?? null,
                    'report_file_id' => $item['reportFileId'] ?? null,
                    'report_date' => $item['reportPublicDate'] ?? null,
                ];
            })->values()->all();

            TenderHntdt::updateOrCreate(
                [
                    'notify_no' => $tender->notify_no,
                    'notify_version' => $version['notifyVersion'],
                ],
                [
                    'data' => [
                        'version' => $version['notifyVersion'],
                        'rows' => $rows
                    ],
                    'raw' => $version
                ]
            );
        }
    }
}