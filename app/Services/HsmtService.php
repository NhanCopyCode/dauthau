<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tender;

// class HsmtService
// {
//     public function handle(int $tenderId): void
//     {
//         $tender = Tender::findOrFail($tenderId);
//         $data = $this->fetchHsmt(
//             $tender->notify_id,
//             $tenderId,
//             $tender->process_apply
//         );

//         if (!empty($data)) {
//             $this->saveHsmt($tenderId, $data);
//         }
//     }



//     private function fetchHsmt(string $id, int $tenderId, ?string $processApplyFromDb = null): array
//     {
//         if (empty($processApplyFromDb)) {
//             return [];
//         }

//         $res = $this->callApi([
//             'id' => $id,
//             'processApply' => $processApplyFromDb,
//         ]);

//         if (empty($res)) {
//             return [];
//         }

//         $chapters = collect($res['bidaInvChapterConfList'] ?? [])->map(function ($item) {
//             return [
//                 'api_id' => $item['id'] ?? null,
//                 'code' => $item['code'] ?? null,
//                 'pcode' => $item['pcode'] ?? null,

//                 'name' => $item['name'] ?? null,
//                 'name_en' => $item['nameEn'] ?? null,
//                 'description' => $item['description'] ?? null,

//                 'order_index' => $item['orderIndex'] ?? 0,
//                 'level' => $item['lev'] ?? 0,

//                 'is_webform' => $item['isWebform'] ?? false,

//                 'bid_form' => $item['bidForm'] ?? null,
//                 'bid_field' => $item['bidField'] ?? null,
//                 'bid_file' => $item['bidFile'] ?? null,
//                 'contract_type' => $item['contractType'] ?? null,

//                 'process_type' => $item['processType'] ?? null,

//                 'raw' => $item
//             ];
//         });

//         $biddings = collect($res['bidoInvBiddingDTO'] ?? [])->map(function ($item) {
//             return [
//                 'chapter_code' => $item['chapterCode'] ?? null,
//                 'form_code' => $item['formCode'] ?? null,
//                 'data' => $this->safeJsonDecode($item['formValue'] ?? null),
//                 'raw' => $item
//             ];
//         });

//         return [
//             'chapters' => $chapters,
//             'biddings' => $biddings,
//         ];
//     }

//     private function callApi(array $payload): array
//     {
//         $endpoint = config("crawler.hsmt");
//         $token = config('crawler.token');

//         $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/{$endpoint}?token={$token}";

//         try {

//             $startedAt = microtime(true);

//             $response = Http::timeout(20)
//                 ->retry(3, 1000)
//                 ->withHeaders([
//                     'accept' => 'application/json',
//                     'content-type' => 'application/json',
//                 ])
//                 ->withOptions(['verify' => false])
//                 ->post($url, $payload);

//             $duration = round((microtime(true) - $startedAt) * 1000);

//             if (!$response->successful()) {
//                 return [];
//             }


//             $json = json_decode($response->body(), true);

//             if (json_last_error() !== JSON_ERROR_NONE) {

//                 Log::error('INVALID JSON RESPONSE', [
//                     'json_error' => json_last_error_msg(),
//                     'body' => mb_substr($response->body(), 0, 3000),
//                     'payload' => $payload,
//                 ]);

//                 return [];
//             }

//             return is_array($json) ? $json : [];
//         } catch (\Throwable $e) {
//             Log::error("HSMT API ERROR", [
//                 'error' => $e->getMessage(),
//                 'payload' => $payload,
//                 'url' => $url,
//             ]);

//             return [];
//         }
//     }



//     private function saveHsmt(int $tenderId, array $data): void
//     {
//         try {

//             $chapters = collect($data['chapters'] ?? []);
//             $biddings = collect($data['biddings'] ?? []);

//             $biddingMap = $biddings->groupBy('chapter_code');

//             $rows = [];

//             foreach ($chapters as $item) {

//                 $chapterCode = $item['code'] ?? null;

//                 $biddingItems = $biddingMap[$chapterCode] ?? collect();

//                 // 👉 extract attachments
//                 $attachments = $biddingItems->map(function ($bid) {

//                     $data = $bid['data'] ?? null;

//                     if (!$data) return null;

//                     return [
//                         'form_code' => $bid['form_code'] ?? null,
//                         'files' => $data
//                     ];
//                 })->filter()->values();

//                 $rows[] = [
//                     'tender_id' => $tenderId,

//                     'api_id' => $item['api_id'] ?? null,
//                     'code' => $item['code'] ?? null,
//                     'pcode' => $item['pcode'] ?? null,

//                     'name' => $item['name'] ?? null,
//                     'name_en' => $item['name_en'] ?? null,
//                     'description' => $item['description'] ?? null,

//                     'order_index' => $item['order_index'] ?? 0,
//                     'level' => $item['level'] ?? 0,

//                     'is_webform' => $item['is_webform'] ?? false,

//                     'bid_form' => $item['bid_form'] ?? null,
//                     'bid_field' => $item['bid_field'] ?? null,
//                     'bid_file' => $item['bid_file'] ?? null,
//                     'contract_type' => $item['contract_type'] ?? null,

//                     'process_type' => $item['process_type'] ?? null,


//                     'attachments' => $attachments->isEmpty() ? null : json_encode($attachments),
//                     'bidding_raw' => $biddingItems->isEmpty() ? null : json_encode($biddingItems),

//                     'raw' => json_encode($item['raw'] ?? $item),

//                     'created_at' => now(),
//                     'updated_at' => now(),
//                 ];
//             }

//             DB::table('tender_hsmt_chapters')->upsert(
//                 $rows,
//                 ['tender_id', 'code'],
//                 [
//                     'name',
//                     'name_en',
//                     'description',
//                     'order_index',
//                     'level',
//                     'pcode',
//                     'is_webform',
//                     'attachments',
//                     'bidding_raw',
//                     'updated_at',
//                     'raw'
//                 ]
//             );
//         } catch (\Throwable $e) {
//             Log::error("SAVE HSMT FAILED", [
//                 'tender_id' => $tenderId,
//                 'error' => $e->getMessage()
//             ]);
//         }
//     }

//     private function safeJsonDecode(?string $json)
//     {
//         if (empty($json)) return null;

//         try {
//             return json_decode($json, true);
//         } catch (\Throwable $e) {
//             return null;
//         }
//     }
// }



class HsmtService
{
 

    public function handle(int $tenderId): void
    {
        try {

            $tender = Tender::findOrFail($tenderId);

         

            if (empty($tender->notify_id)) {
                return;
            }

            if (empty($tender->process_apply)) {
                return;
            }


            $raw = $this->fetchRaw(
                notifyId: $tender->notify_id,
                processApply: $tender->process_apply
            );

            if (empty($raw)) {
                return;
            }

          
            $view = $this->transformView($raw);

           
            $this->saveHsmt(
                tender: $tender,
                raw: $raw,
                view: $view
            );

        } catch (\Throwable $e) {

            Log::error('HSMT HANDLE FAILED', [
                'tender_id' => $tenderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

  
    private function fetchRaw(
        string $notifyId,
        string $processApply
    ): array {

        return $this->callApi([
            'id' => $notifyId,
            'processApply' => $processApply,
        ]);
    }

  

    private function transformView(array $raw): array
    {
        $chapters = collect(
            $raw['bidaInvChapterConfList'] ?? []
        );

        $biddings = collect(
            $raw['bidoInvBiddingDTO'] ?? []
        );

        $biddingMap = $biddings->groupBy('chapterCode');

      
        $parts = [
            'P1' => 'Phần 1: Thủ tục đấu thầu',
            'P2' => 'Phần 2: Yêu cầu về kỹ thuật',
            'P3' => 'Phần 3: Điều kiện hợp đồng và biểu mẫu hợp đồng',
        ];

        $result = [];

        foreach ($parts as $pcode => $partName) {


            $children = $chapters
                ->where('pcode', $pcode)
                ->sortBy('orderIndex')
                ->values()
                ->map(function ($chapter) use ($biddingMap) {

                    $chapterCode = $chapter['code'] ?? null;

                
                    $biddingItems = collect(
                        $biddingMap[$chapterCode] ?? []
                    );

                  
                    $attachments = $biddingItems
                        ->map(function ($bid) {

                            $decoded = $this->safeJsonDecode(
                                $bid['formValue'] ?? null
                            );

                            if (!$decoded) {
                                return null;
                            }

                            return [
                                'form_code' => $bid['formCode'] ?? null,
                                'files' => $decoded,
                            ];
                        })
                        ->filter()
                        ->values()
                        ->toArray();

                    return [

                        'code' => $chapter['code'] ?? null,

                        'name' => $chapter['name'] ?? null,

                        'level' => $chapter['lev'] ?? 0,

                        'order_index' => $chapter['orderIndex'] ?? 0,

                        'is_webform' => (bool) (
                            $chapter['isWebform'] ?? false
                        ),

                        'attachments' => $attachments,
                    ];
                })
                ->filter(fn($x) => !empty($x['name']))
                ->values();

            if ($children->isEmpty()) {
                continue;
            }

            $result[] = [
                'code' => $pcode,
                'name' => $partName,
                'children' => $children->toArray(),
            ];
        }

        return collect($result)
            ->values()
            ->map(function ($part, $partIndex) {

                $part['number'] = (string)($partIndex + 1);

                $part['children'] = collect($part['children'])
                    ->values()
                    ->map(function ($child, $childIndex) use ($partIndex) {

                        $child['number'] =
                            ($partIndex + 1)
                            . '.'
                            . ($childIndex + 1);

                        return $child;
                    })
                    ->toArray();

                return $part;
            })
            ->toArray();
    }

   
    private function saveHsmt(
        Tender $tender,
        array $raw,
        array $view
    ): void {

        try {

            DB::table('tender_hsmts')->updateOrInsert(

                [
                    'tender_id' => $tender->id,
                ],

                [
                
                    'notify_id' => $tender->notify_id,

                    'type' => 'online',

                    'process_apply' => $tender->process_apply,

                  
                    'view_json' => json_encode(
                        $view,
                        JSON_UNESCAPED_UNICODE
                    ),

                

                    'raw_json' => json_encode(
                        $raw,
                        JSON_UNESCAPED_UNICODE
                    ),

               

                    'chapter_count' => collect($view)
                        ->sum(function ($part) {
                            return count($part['children'] ?? []);
                        }),

                    'attachment_count' => collect($view)
                        ->flatMap(function ($part) {
                            return $part['children'] ?? [];
                        })
                        ->sum(function ($child) {
                            return count(
                                $child['attachments'] ?? []
                            );
                        }),

                    'updated_at' => now(),

                    'created_at' => now(),
                ]
            );

        } catch (\Throwable $e) {

            Log::error('SAVE HSMT FAILED', [
                'tender_id' => $tender->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function callApi(array $payload): array
    {
        $endpoint = config('crawler.hsmt');

        $token = config('crawler.token');

        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/{$endpoint}?token={$token}";

        try {
            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false,
                ])
                ->post($url, $payload);

           
            if (!$response->successful()) {

                Log::warning('HSMT API FAILED', [
                    'status' => $response->status(),
                    'payload' => $payload,
                ]);

                return [];
            }

            $json = json_decode(
                $response->body(),
                true
            );

            if (json_last_error() !== JSON_ERROR_NONE) {

                Log::error('INVALID JSON RESPONSE', [

                    'json_error' => json_last_error_msg(),

                    'body' => mb_substr(
                        $response->body(),
                        0,
                        3000
                    ),

                    'payload' => $payload,
                ]);

                return [];
            }

            return is_array($json)
                ? $json
                : [];

        } catch (\Throwable $e) {

            Log::error('HSMT API ERROR', [

                'error' => $e->getMessage(),

                'payload' => $payload,

                'url' => $url,
            ]);

            return [];
        }
    }

   
    private function safeJsonDecode(?string $json): mixed
    {
        if (empty($json)) {
            return null;
        }

        try {

            return json_decode($json, true);

        } catch (\Throwable $e) {

            return null;
        }
    }
}
