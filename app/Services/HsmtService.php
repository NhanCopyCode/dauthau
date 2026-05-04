<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tender;

class HsmtService
{
    public function handle(int $tenderId): void
    {
        $tender = Tender::findOrFail($tenderId);
        $data = $this->fetchHsmt(
            $tender->notify_id,
            $tenderId,
            $tender->process_apply
        );

        if (!empty($data)) {
            $this->saveHsmt($tenderId, $data);
        }
    }

 

    private function fetchHsmt(string $id, int $tenderId, ?string $processApplyFromDb = null): array
    {
        if (empty($processApplyFromDb)) {
            return [];
        }

        $res = $this->callApi([
            'id' => $id,
            'processApply' => $processApplyFromDb,
        ]);

        if (empty($res)) {
            return [];
        }

        $chapters = collect($res['bidaInvChapterConfList'] ?? [])->map(function ($item) {
            return [
                'api_id' => $item['id'] ?? null,
                'code' => $item['code'] ?? null,
                'pcode' => $item['pcode'] ?? null,

                'name' => $item['name'] ?? null,
                'name_en' => $item['nameEn'] ?? null,
                'description' => $item['description'] ?? null,

                'order_index' => $item['orderIndex'] ?? 0,
                'level' => $item['lev'] ?? 0,

                'is_webform' => $item['isWebform'] ?? false,

                'bid_form' => $item['bidForm'] ?? null,
                'bid_field' => $item['bidField'] ?? null,
                'bid_file' => $item['bidFile'] ?? null,
                'contract_type' => $item['contractType'] ?? null,

                'process_type' => $item['processType'] ?? null,

                'raw' => $item
            ];
        });

        $biddings = collect($res['bidoInvBiddingDTO'] ?? [])->map(function ($item) {
            return [
                'chapter_code' => $item['chapterCode'] ?? null,
                'form_code' => $item['formCode'] ?? null,
                'data' => $this->safeJsonDecode($item['formValue'] ?? null),
                'raw' => $item
            ];
        });

        return [
            'chapters' => $chapters,
            'biddings' => $biddings,
        ];
    }

    private function callApi(array $payload): array
    {
        $endpoint = config("crawler.hsmt");
        $token = config('crawler.token');

        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/{$endpoint}?token={$token}";

        try {
            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                ->withOptions(['verify' => false])
                ->post($url, $payload);

            if (!$response->successful()) {
                return [];
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("HSMT API ERROR", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [];
        }
    }

  

    private function saveHsmt(int $tenderId, array $data): void
    {
        try {

            $chapters = collect($data['chapters'] ?? []);
            $biddings = collect($data['biddings'] ?? []);

            $biddingMap = $biddings->groupBy('chapter_code');

            $rows = [];

            foreach ($chapters as $item) {

                $chapterCode = $item['code'] ?? null;

                $biddingItems = $biddingMap[$chapterCode] ?? collect();

                // 👉 extract attachments
                $attachments = $biddingItems->map(function ($bid) {

                    $data = $bid['data'] ?? null;

                    if (!$data) return null;

                    return [
                        'form_code' => $bid['form_code'] ?? null,
                        'files' => $data
                    ];
                })->filter()->values();

                $rows[] = [
                    'tender_id' => $tenderId,

                    'api_id' => $item['api_id'] ?? null,
                    'code' => $item['code'] ?? null,
                    'pcode' => $item['pcode'] ?? null,

                    'name' => $item['name'] ?? null,
                    'name_en' => $item['name_en'] ?? null,
                    'description' => $item['description'] ?? null,

                    'order_index' => $item['order_index'] ?? 0,
                    'level' => $item['level'] ?? 0,

                    'is_webform' => $item['is_webform'] ?? false,

                    'bid_form' => $item['bid_form'] ?? null,
                    'bid_field' => $item['bid_field'] ?? null,
                    'bid_file' => $item['bid_file'] ?? null,
                    'contract_type' => $item['contract_type'] ?? null,

                    'process_type' => $item['process_type'] ?? null,

                    
                    'attachments' => $attachments->isEmpty() ? null : json_encode($attachments),
                    'bidding_raw' => $biddingItems->isEmpty() ? null : json_encode($biddingItems),

                    'raw' => json_encode($item['raw'] ?? $item),

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('tender_hsmt_chapters')->upsert(
                $rows,
                ['tender_id', 'code'],
                [
                    'name',
                    'name_en',
                    'description',
                    'order_index',
                    'level',
                    'pcode',
                    'is_webform',
                    'attachments',
                    'bidding_raw',
                    'updated_at',
                    'raw'
                ]
            );
        } catch (\Throwable $e) {
            Log::error("SAVE HSMT FAILED", [
                'tender_id' => $tenderId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function safeJsonDecode(?string $json)
    {
        if (empty($json)) return null;

        try {
            return json_decode($json, true);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
