<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// class TenderCrawlerService
// {
//     public function crawlPage(int $page = 0, int $pageSize = 50): array
//     {
//         $token = config('crawler.token');
//         $baseUrl = config('crawler.base_url');

//         $payload = $this->buildFullPayload($page, $pageSize);
//         $response = Http::timeout(30)
//             ->retry(3, 1000)
//             ->withHeaders([
//                 "accept" => "application/json",
//                 "content-type" => "application/json",
//             ])->withOptions([
//                 'verify' => false
//             ])
//             ->post(
//                 "{$baseUrl}/o/egp-portal-contractor-selection-v2/services/smart/search?token={$token}",
//                 $payload
//             );

//         if (!$response->successful()) {
//             Log::error("API ERROR", [
//                 'status' => $response->status(),
//                 'body' => $response->body()
//             ]);
//             throw new \Exception("HTTP Error: " . $response->status());
//         }

//         $data = $response->json();


//         return $data ?? [];
//     }

//     private function buildFullPayload(int $page, int $pageSize): array
//     {
//         $now = now()->toISOString();

//         return [[
//             "pageSize" => $pageSize,
//             "pageNumber" => $page,
//             "query" => [[
//                 "index" => "es-contractor-selection",
//                 "keyWord" => "",
//                 "matchType" => "all-1",
//                 "matchFields" => ["notifyNo", "bidName"],
//                 "filters" => [
//                     [
//                         "fieldName" => "type",
//                         "searchType" => "in",
//                         "fieldValues" => ["es-notify-contractor"]
//                     ],
//                     [
//                         "fieldName" => "caseKHKQ",
//                         "searchType" => "not_in",
//                         "fieldValues" => ["1"]
//                     ],
//                     [
//                         "fieldName" => "bidCloseDate",
//                         "searchType" => "range",
//                         "from" => $now,
//                         "to" => null
//                     ]
//                 ]
//             ]]
//         ]];
//     }

//     private function buildDailyPayload(
//         int $page,
//         int $pageSize,
//         Carbon $date
//     ): array {

//         $from = $date
//             ->copy()
//             ->startOfDay()
//             ->toISOString();

//         $to = $date
//             ->copy()
//             ->endOfDay()
//             ->toISOString();

//         return [[
//             "pageSize" => $pageSize,
//             "pageNumber" => $page,
//             "query" => [[
//                 "index" => "es-contractor-selection",
//                 "matchType" => "all-1",
//                 "matchFields" => [
//                     "notifyNo",
//                     "bidName"
//                 ],
//                 "filters" => [

//                     [
//                         "fieldName" => "publicDate",
//                         "searchType" => "range",
//                         "from" => $from,
//                         "to" => $to
//                     ],

//                     [
//                         "fieldName" => "type",
//                         "searchType" => "in",
//                         "fieldValues" => [
//                             "es-notify-contractor"
//                         ]
//                     ],

//                     [
//                         "fieldName" => "caseKHKQ",
//                         "searchType" => "not_in",
//                         "fieldValues" => ["1"]
//                     ]
//                 ]
//             ]]
//         ]];
//     }


//     public function saveItems(array $items): array
//     {
//         if (empty($items)) return [];

//         $tenders = [];

//         foreach ($items as $item) {

//             $getInt = fn($key) => (int) data_get($item, $key, 0);
//             $getString = fn($key) => data_get($item, $key);
//             $getJson = fn($key) => data_get($item, $key, []);

//             $score = data_get($item, 'score');
//             $score = is_numeric($score) ? $score : null;

//             $tender = Tender::updateOrCreate(
//                 ['egp_id' => $getString('id')],
//                 [
//                     'notify_id' => $getString('notifyId'),
//                     'bid_id' => $getString('bidId'),

//                     'notify_no' => $getString('notifyNo'),
//                     'notify_version' => $getString('notifyVersion'),
//                     'notify_no_stand' => $getString('notifyNoStand'),

//                     'name' => data_get($item, 'bidName.0'),
//                     'bid_names' => $getJson('bidName'),

//                     'investor' => $getString('investorName'),
//                     'investor_code' => $getString('investorCode'),

//                     'province' => data_get($item, 'locations.0.provName'),
//                     'locations' => $getJson('locations'),

//                     'bid_close_date' => $getString('bidCloseDate'),
//                     'bid_open_date' => $getString('bidOpenDate'),
//                     'public_date' => $getString('publicDate'),
//                     'original_public_date' => $getString('originalPublicDate'),

//                     'plan_no' => $getString('planNo'),
//                     'plan_type' => $getString('planType'),

//                     'bid_form' => $getString('bidForm'),
//                     'bid_mode' => $getString('bidMode'),
//                     'process_apply' => $getString('processApply'),

//                     'invest_field' => data_get($item, 'investField.0'),
//                     'invest_fields' => $getJson('investField'),

//                     'bid_price' => (float) data_get($item, 'bidPrice.0', 0),

//                     'status' => $getString('status'),
//                     'status_for_notify' => $getString('statusForNotify'),

//                     'type' => $getString('type'),
//                     'step_code' => $getString('stepCode'),

//                     'num_petition' => $getInt('numPetition'),
//                     'num_clarify_req' => $getInt('numClarifyReq'),
//                     'num_bidder_tech' => $getInt('numBidderTech'),
//                     'num_petition_hsmt' => $getInt('numPetitionHsmt'),
//                     'num_petition_lcnt' => $getInt('numPetitionLcnt'),
//                     'num_petition_kqlcnt' => $getInt('numPetitionKqlcnt'),

//                     'is_internet' => $getInt('isInternet'),
//                     'is_domestic' => $getInt('isDomestic'),
//                     'is_medicine' => $getInt('isMedicine'),

//                     'created_by' => $getString('createdBy'),
//                     'score' => $score,

//                     'last_seen_at' => now(),
//                     'is_active' => 1,
//                 ]
//             );

//             $tenders[] = $tender;
//         }

//         return $tenders;
//     }
// }


class TenderCrawlerService
{
    protected string $token;

    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('crawler.token', '');

        $this->baseUrl = config('crawler.base_url', '');
    }



    public function crawlPage(
        int $page = 1,
        int $pageSize = 50
    ): array {

        $payload = $this->buildFullPayload(
            $page,
            $pageSize
        );


        return $this->request($payload);
    }



    public function crawlDailyPage(
        int $page,
        Carbon $date,
        int $pageSize = 50
    ): array {

        $payload = $this->buildDailyPayload(
            $page,
            $pageSize,
            $date
        );

        return $this->request($payload);
    }



    public function crawlRangePage(
        int $page,
        Carbon $from,
        Carbon $to,
        int $pageSize = 50
    ): array {

        $payload = $this->buildRangePayload(
            $page,
            $pageSize,
            $from,
            $to
        );

        return $this->request($payload);
    }

    private function request(
        array $payload
    ): array {

        $url = sprintf(
            '%s/o/egp-portal-contractor-selection-v2/services/smart/search?token=%s',
            $this->baseUrl,
            $this->token
        );


        $startedAt = microtime(true);

        try {

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false,
                ])
                ->post($url, $payload);

            $duration = round(
                microtime(true) - $startedAt,
                3
            );

            Log::info('CRAWLER API RESPONSE', [

                'status' => $response->status(),

                'duration_seconds' => $duration,
            ]);

            if (!$response->successful()) {

                Log::error('CRAWLER API FAILED', [

                    'status' => $response->status(),

                    'body' => $response->body(),
                ]);

                throw new \Exception(
                    'Crawler API Error: ' .
                        $response->status()
                );
            }

            return $this->normalizeResponse(
                $response->json()
            );
        } catch (\Throwable $e) {

            Log::error('CRAWLER REQUEST EXCEPTION', [

                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }



    private function normalizeResponse(
        ?array $data
    ): array {

        return [
            'content' => data_get(
                $data,
                'page.content',
                []
            ),

            'total_elements' => data_get(
                $data,
                'page.totalElements',
                0
            ),

            'total_pages' => data_get(
                $data,
                'page.totalPages',
                0
            ),

            'raw' => $data,
        ];
    }


    // private function buildFullPayload(
    //     int $page,
    //     int $pageSize
    // ): array {

    //     return [[
    //         'pageSize' => $pageSize,

    //         'pageNumber' => $page,

    //         'query' => [[

    //             'index' => 'es-contractor-selection',

    //             'keyWord' => '',

    //             'matchType' => 'all-1',

    //             'matchFields' => [
    //                 'notifyNo',
    //                 'bidName',
    //             ],

    //             'filters' => [

    //                 [
    //                     'fieldName' => 'type',

    //                     'searchType' => 'in',

    //                     'fieldValues' => [
    //                         'es-notify-contractor'
    //                     ]
    //                 ],

    //                 [
    //                     'fieldName' => 'caseKHKQ',
    //                     'searchType' => 'not_in',
    //                     'fieldValues' => ['1']
    //                 ],
    //             ]
    //         ]]
    //     ]];
    // }
    // private function buildFullPayload(
    //     int $page,
    //     int $pageSize
    // ): array {

    //     return [[
    //         'pageSize' => $pageSize,

    //         'pageNumber' => $page,

    //         'query' => [[

    //             'index' => 'es-contractor-selection',

    //             'keyWord' => '',

    //             'matchType' => 'all-1',

    //             'matchFields' => [
    //                 'notifyNo',
    //                 'bidName',
    //             ],

    //             'filters' => [

    //                 [
    //                     'fieldName' => 'type',

    //                     'searchType' => 'in',

    //                     'fieldValues' => [
    //                         'es-notify-contractor'
    //                     ]
    //                 ],

    //                 [
    //                     'fieldName' => 'caseKHKQ',

    //                     'searchType' => 'not_in',

    //                     'fieldValues' => ['1']
    //                 ],

    //                 [
    //                     'fieldName' => 'bidCloseDate',

    //                     'searchType' => 'range',

    //                     'from' => now()
    //                         ->copy()
    //                         ->startOfDay()
    //                         ->format('Y-m-d\T00:00:00.000\Z'),

    //                     'to' => null,
    //                 ],
    //             ]
    //         ]]
    //     ]];
    // }

    private function buildFullPayload(
        int $page,
        int $pageSize
    ): array {

        return [[
            'pageSize' => $pageSize,

            'pageNumber' => $page,

            'query' => [[

                'index' => 'es-contractor-selection',

                'keyWord' => '',

                'matchType' => 'all-1',

                'matchFields' => [
                    'notifyNo',
                    'bidName',
                ],

                'filters' => [

                    [
                        'fieldName' => 'type',

                        'searchType' => 'in',

                        'fieldValues' => [
                            'es-notify-contractor'
                        ]
                    ],

                    [
                        'fieldName' => 'caseKHKQ',

                        'searchType' => 'not_in',

                        'fieldValues' => ['1']
                    ],

                    [
                        'fieldName' => 'bidCloseDate',

                        'searchType' => 'range',

                        'from' => now('Asia/Ho_Chi_Minh')
                            ->format('Y-m-d\TH:i:s.v\Z'),

                        'to' => null,
                    ],
                ]
            ]]
        ]];
    }

    private function buildDailyPayload(
        int $page,
        int $pageSize,
        Carbon $date
    ): array {

        return $this->buildRangePayload(
            $page,
            $pageSize,
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        );
    }


    // private function buildRangePayload(
    //     int $page,
    //     int $pageSize,
    //     Carbon $from,
    //     Carbon $to
    // ): array {

    //     return [[

    //         'pageSize' => $pageSize,

    //         'pageNumber' => $page,

    //         'query' => [[

    //             'index' => 'es-contractor-selection',

    //             'keyWord' => '',

    //             'matchType' => 'all-1',

    //             'matchFields' => [
    //                 'notifyNo',
    //                 'bidName',
    //             ],

    //             'filters' => [

    //                 [
    //                     'fieldName' => 'publicDate',

    //                     'searchType' => 'range',

    //                     'from' => $from->toISOString(),

    //                     'to' => $to->toISOString(),
    //                 ],

    //                 [
    //                     'fieldName' => 'type',

    //                     'searchType' => 'in',

    //                     'fieldValues' => [
    //                         'es-notify-contractor'
    //                     ]
    //                 ],

    //                 [
    //                     'fieldName' => 'caseKHKQ',

    //                     'searchType' => 'not_in',

    //                     'fieldValues' => ['1']
    //                 ]
    //             ]
    //         ]]
    //     ]];
    // }
    // private function buildRangePayload(
    //     int $page,
    //     int $pageSize,
    //     Carbon $from,
    //     Carbon $to
    // ): array {

    //     return [[
    //         'pageSize' => $pageSize,
    //         'pageNumber' => $page,
    //         'query' => [[
    //             'index' => 'es-contractor-selection',
    //             'keyWord' => '',
    //             'matchType' => 'all-1',
    //             'matchFields' => [
    //                 'notifyNo',
    //                 'bidName',
    //             ],
    //             'filters' => [

    //                 [
    //                     'fieldName' => 'publicDate',
    //                     'searchType' => 'range',
    //                     'from' => $from
    //                         ->copy()
    //                         ->startOfDay()
    //                         ->format('Y-m-d\T00:00:00.000\Z'),

    //                     'to' => $to
    //                         ->copy()
    //                         ->endOfDay()
    //                         ->format('Y-m-d\T23:59:59.059\Z'),
    //                 ],

    //                 [
    //                     'fieldName' => 'type',
    //                     'searchType' => 'in',
    //                     'fieldValues' => [
    //                         'es-notify-contractor'
    //                     ]
    //                 ],

    //                 [
    //                     'fieldName' => 'caseKHKQ',
    //                     'searchType' => 'not_in',
    //                     'fieldValues' => ['1']
    //                 ]
    //             ]
    //         ]]
    //     ]];
    // }

    private function buildRangePayload(
        int $page,
        int $pageSize,
        Carbon $from,
        Carbon $to
    ): array {

        return [[
            'pageSize' => min($pageSize, 50),
            'pageNumber' => $page,

            'query' => [[
                'index' => 'es-contractor-selection',

                'matchType' => 'all-1',

                'matchFields' => [
                    'notifyNo',
                    'bidName',
                ],

                'filters' => [
                    [
                        'fieldName' => 'publicDate',
                        'searchType' => 'range',

                        'from' => $from
                            ->copy()
                            ->format('Y-m-d\T00:00:00.000\Z'),

                        'to' => $to
                            ->copy()
                            ->format('Y-m-d\T23:59:59.059\Z'),
                    ],

                    [
                        'fieldName' => 'bidCloseDate',
                        'searchType' => 'range',

                        'from' => now()
                            ->format('Y-m-d\TH:i:s.v\Z'),

                        'to' => null,
                    ],

                    [
                        'fieldName' => 'type',
                        'searchType' => 'in',
                        'fieldValues' => [
                            'es-notify-contractor',
                        ],
                    ],

                    [
                        'fieldName' => 'caseKHKQ',
                        'searchType' => 'not_in',
                        'fieldValues' => [
                            '1',
                        ],
                    ],
                ],
            ]],
        ]];
    }
    public function saveItems(
        array $items
    ): array {

        if (empty($items)) {
            return [];
        }

        $tenders = [];

        DB::beginTransaction();

        try {

            foreach ($items as $item) {

                $mapped = $this->mapTenderData(
                    $item
                );

                $tender = Tender::updateOrCreate(
                    [
                        'egp_id' => $mapped['egp_id']
                    ],
                    $mapped
                );

                $tenders[] = $tender;
            }

            DB::commit();

            return $tenders;
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('SAVE ITEMS FAILED', [

                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }


    private function mapTenderData(
        array $item
    ): array {

        $getInt = fn($key)
        => (int) data_get($item, $key, 0);

        $getString = fn($key)
        => data_get($item, $key);

        $getJson = fn($key)
        => data_get($item, $key, []);

        $score = data_get($item, 'score');

        return [

            'egp_id' => $getString('id'),

            'notify_id' => $getString('notifyId'),

            'bid_id' => $getString('bidId'),

            'notify_no' => $getString('notifyNo'),

            'notify_version' => $getString('notifyVersion'),

            'notify_no_stand' => $getString('notifyNoStand'),

            'name' => data_get($item, 'bidName.0'),

            'bid_names' => $getJson('bidName'),

            'investor' => $getString('investorName'),

            'investor_code' => $getString('investorCode'),

            'province' => data_get(
                $item,
                'locations.0.provName'
            ),

            'locations' => $getJson('locations'),

            'bid_close_date' => $getString('bidCloseDate'),

            'bid_open_date' => $getString('bidOpenDate'),

            'public_date' => $getString('publicDate'),

            'original_public_date'
            => $getString('originalPublicDate'),

            'plan_no' => $getString('planNo'),

            'plan_type' => $getString('planType'),

            'bid_form' => $getString('bidForm'),

            'bid_mode' => $getString('bidMode'),

            'process_apply' => $getString('processApply'),

            'invest_field'
            => data_get($item, 'investField.0'),

            'invest_fields'
            => $getJson('investField'),

            'bid_price'
            => (float) data_get(
                $item,
                'bidPrice.0',
                0
            ),

            'status' => $getString('status'),

            'status_for_notify'
            => $getString('statusForNotify'),

            'type' => $getString('type'),

            'step_code' => $getString('stepCode'),

            'num_petition'
            => $getInt('numPetition'),

            'num_clarify_req'
            => $getInt('numClarifyReq'),

            'num_bidder_tech'
            => $getInt('numBidderTech'),

            'num_petition_hsmt'
            => $getInt('numPetitionHsmt'),

            'num_petition_lcnt'
            => $getInt('numPetitionLcnt'),

            'num_petition_kqlcnt'
            => $getInt('numPetitionKqlcnt'),

            'is_internet'
            => $getInt('isInternet'),

            'is_domestic'
            => $getInt('isDomestic'),

            'is_medicine'
            => $getInt('isMedicine'),

            'created_by'
            => $getString('createdBy'),

            'score'
            => is_numeric($score)
                ? $score
                : null,

            'last_seen_at' => now(),

            'is_active' => 1,
        ];
    }
}
