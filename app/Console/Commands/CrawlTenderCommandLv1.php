<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrawlTenderCommandLv1 extends Command
{
    protected $signature = 'crawl:tenders:basic';
    protected $description = 'Crawl tenders (basic version)';

    public function handle()
    {
        $token = env('CRAWLER_TOKEN');
        $baseUrl = env('CRAWLER_BASE_URL');

        $pageSize = env('CRAWLER_PAGE_SIZE', 20);
        $delay = env('CRAWLER_DELAY', 1);

        $page = 0;

        while (true) {

            $this->info("Crawling page: $page");

            $now = Carbon::now()->toISOString();

            $payload = [[
                "pageSize" => (int)$pageSize,
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

            try {

                $response = Http::withHeaders([
                    "accept" => "application/json, text/plain, */*",
                    "content-type" => "application/json",
                    "origin" => "https://muasamcong.mpi.gov.vn",
                    "referer" => "https://muasamcong.mpi.gov.vn/web/guest/contractor-selection",
                    "user-agent" => "Mozilla/5.0"
                ])->withOptions([
                    'verify' => 'C:\laragon\etc\ssl\cacert.pem'
                ])->post("$baseUrl/o/egp-portal-contractor-selection-v2/services/smart/search?token=$token", $payload);

                if (!$response->ok()) {
                    $this->error("HTTP Error: " . $response->status());
                    break;
                }

                $data = $response->json();

                if (!$data || !isset($data['page']['content'])) {
                    $this->warn("Không còn dữ liệu");
                    break;
                }

                $items = $data['page']['content'];

                if (empty($items)) {
                    $this->warn("Hết dữ liệu");
                    break;
                }

                foreach ($items as $item) {

                    DB::table('tenders')->updateOrInsert(
                        ['egp_id' => $item['id']],
                        [
                            'notify_id' => $item['notifyId'] ?? null,
                            'notify_no' => $item['notifyNo'] ?? null,
                            'notify_version' => $item['notifyVersion'] ?? null,
                            'bid_id' => $item['bidId'] ?? null,
                            'notify_no_stand' => $item['notifyNoStand'] ?? null,
                            'name' => $item['bidName'][0] ?? null,
                            'investor' => $item['investorName'] ?? null,
                            'investor_code' => $item['investorCode'] ?? null,
                            'province' => $item['locations'][0]['provName'] ?? null,
                            'bid_close_date' => $item['bidCloseDate'] ?? null,
                            'bid_open_date' => $item['bidOpenDate'] ?? null,
                            'public_date' => $item['publicDate'] ?? null,
                            'plan_no' => $item['planNo'] ?? null,
                            'plan_type' => $item['planType'] ?? null,
                            'bid_form' => $item['bidForm'] ?? null,
                            'bid_mode' => $item['bidMode'] ?? null,
                            'invest_field' => $item['investField'][0] ?? null,
                            'bid_price' => $item['bidPrice'][0] ?? null,
                            'is_internet' => $item['isInternet'] ?? null,
                            'is_domestic' => $item['isDomestic'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                $page++;

                sleep($delay);
            } catch (\Exception $e) {
                $this->error("Crawler error:");
                $this->error($e->getMessage());
                break;
            }
        }

        $this->info("Done crawling");
    }
}
