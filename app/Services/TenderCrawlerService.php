<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TenderCrawlerService
{
    public function crawlPage(int $page = 0, int $pageSize = 50): array
    {
        $token = config('crawler.token');
        $baseUrl = config('crawler.base_url');

        $payload = $this->buildPayload($page, $pageSize);


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


        return $data ?? [];
    }

    private function buildPayload(int $page, int $pageSize): array
    {
        $now = now()->toISOString();

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



    public function saveItems(array $items): array
    {
        if (empty($items)) return [];

        $tenders = [];

        foreach ($items as $item) {

            $getInt = fn($key) => (int) data_get($item, $key, 0);
            $getString = fn($key) => data_get($item, $key);
            $getJson = fn($key) => data_get($item, $key, []);

            $score = data_get($item, 'score');
            $score = is_numeric($score) ? $score : null;

            $tender = Tender::updateOrCreate(
                ['egp_id' => $getString('id')],
                [
                    'notify_id' => $getString('notifyId'),
                    'bid_id' => $getString('bidId'),

                    'notify_no' => $getString('notifyNo'),
                    'notify_version' => $getString('notifyVersion'),
                    'notify_no_stand' => $getString('notifyNoStand'),

                    'name' => data_get($item, 'bidName.0'),
                    'bid_names' => $getJson('bidName'),

                    'investor' => $getString('investorName'),
                    'investor_code' => $getString('investorCode'),

                    'province' => data_get($item, 'locations.0.provName'),
                    'locations' => $getJson('locations'),

                    'bid_close_date' => $getString('bidCloseDate'),
                    'bid_open_date' => $getString('bidOpenDate'),
                    'public_date' => $getString('publicDate'),
                    'original_public_date' => $getString('originalPublicDate'),

                    'plan_no' => $getString('planNo'),
                    'plan_type' => $getString('planType'),

                    'bid_form' => $getString('bidForm'),
                    'bid_mode' => $getString('bidMode'),
                    'process_apply' => $getString('processApply'),

                    'invest_field' => data_get($item, 'investField.0'),
                    'invest_fields' => $getJson('investField'),

                    'bid_price' => (float) data_get($item, 'bidPrice.0', 0),

                    'status' => $getString('status'),
                    'status_for_notify' => $getString('statusForNotify'),

                    'type' => $getString('type'),
                    'step_code' => $getString('stepCode'),

                    'num_petition' => $getInt('numPetition'),
                    'num_clarify_req' => $getInt('numClarifyReq'),
                    'num_bidder_tech' => $getInt('numBidderTech'),
                    'num_petition_hsmt' => $getInt('numPetitionHsmt'),
                    'num_petition_lcnt' => $getInt('numPetitionLcnt'),
                    'num_petition_kqlcnt' => $getInt('numPetitionKqlcnt'),

                    'is_internet' => $getInt('isInternet'),
                    'is_domestic' => $getInt('isDomestic'),
                    'is_medicine' => $getInt('isMedicine'),

                    'created_by' => $getString('createdBy'),
                    'score' => $score,

                    'last_seen_at' => now(),
                    'is_active' => 1,
                ]
            );

            $tenders[] = $tender;
        }

        return $tenders;
    }
   
}
