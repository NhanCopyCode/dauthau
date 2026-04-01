<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TenderCrawlerService
{
    public function crawlPage(int $page = 0, int $pageSize = 50): array
    {
        $token = config('crawler.token');
        $baseUrl = config('crawler.base_url');

        $payload = $this->buildPayload($page, $pageSize);

        Log::info("Payload", $payload);

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                "accept" => "application/json",
                "content-type" => "application/json",
            ])->withOptions([
                'verify' => false
            ])
            ->post(
                "{$baseUrl}/o/egp-portal-contractor-selection-v2/services/smart/search?token={$token}",
                $payload
            );

        if (!$response->successful()) {
            Log::error("API ERROR", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception("HTTP Error: " . $response->status());
        }

        $data = $response->json();

        Log::info("Items count: " . count($data['page']['content'] ?? []));

        return $data ?? [];
    }

    private function buildPayload(int $page, int $pageSize): array
    {
        // 🔥 Lấy thời gian hiện tại (UTC chuẩn API)
        $now = now()->toISOString(); // ví dụ: 2026-04-01T07:30:00.000Z

        return [[
            "pageSize" => $pageSize,
            "pageNumber" => $page,
            "query" => [[
                "index" => "es-contractor-selection",
                "keyWord" => "",
                "matchType" => "all-1",
                "matchFields" => ["notifyNo", "bidName"],
                "filters" => [
                    [
                        "fieldName" => "type",
                        "searchType" => "in",
                        "fieldValues" => ["es-notify-contractor"]
                    ],
                    [
                        "fieldName" => "caseKHKQ",
                        "searchType" => "not_in",
                        "fieldValues" => ["1"]
                    ],
                    // 🔥 FIX QUAN TRỌNG
                    [
                        "fieldName" => "bidCloseDate",
                        "searchType" => "range",
                        "from" => $now,
                        "to" => null
                    ]
                ]
            ]]
        ]];
    }


    public function saveItems(array $items): void
    {
        if (empty($items)) {
            return;
        }

        $data = [];

        foreach ($items as $item) {

            $data[] = [
                'egp_id' => Arr::get($item, 'id'),

                'notify_id' => Arr::get($item, 'notifyId'),
                'notify_no' => Arr::get($item, 'notifyNo'),
                'notify_version' => Arr::get($item, 'notifyVersion'),
                'bid_id' => Arr::get($item, 'bidId'),
                'notify_no_stand' => Arr::get($item, 'notifyNoStand'),

                'name' => Arr::get($item, 'bidName.0'),
                'investor' => Arr::get($item, 'investorName'),
                'investor_code' => Arr::get($item, 'investorCode'),

                'province' => Arr::get($item, 'locations.0.provName'),

                'bid_close_date' => Arr::get($item, 'bidCloseDate'),
                'bid_open_date' => Arr::get($item, 'bidOpenDate'),
                'public_date' => Arr::get($item, 'publicDate'),

                'plan_no' => Arr::get($item, 'planNo'),
                'plan_type' => Arr::get($item, 'planType'),

                'bid_form' => Arr::get($item, 'bidForm'),
                'bid_mode' => Arr::get($item, 'bidMode'),

                'invest_field' => Arr::get($item, 'investField.0'),
                'bid_price' => Arr::get($item, 'bidPrice.0'),

                'is_internet' => Arr::get($item, 'isInternet'),
                'is_domestic' => Arr::get($item, 'isDomestic'),

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Tender::upsert(
            $data,
            ['egp_id'],
            [
                'notify_id',
                'notify_no',
                'notify_version',
                'bid_id',
                'notify_no_stand',
                'name',
                'investor',
                'investor_code',
                'province',
                'bid_close_date',
                'bid_open_date',
                'public_date',
                'plan_no',
                'plan_type',
                'bid_form',
                'bid_mode',
                'invest_field',
                'bid_price',
                'is_internet',
                'is_domestic',
                'updated_at'
            ]
        );
    }
}
