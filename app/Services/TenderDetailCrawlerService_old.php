<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenderDetailCrawlerService_old
{
    public function getDetail(Tender $tender): array
    {
        $endpoint = $this->resolveDetailApi($tender);
        $token = config('crawler.token');
        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/" . $endpoint . '?token=' . $token;

        if (!$url) {
            throw new \Exception("Missing config crawler.detail_url");
        }

        $response = Http::timeout(20)
            ->retry(3, 1000)
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])
            ->withOptions([
                'verify' => false
            ])
            ->post($url, [
                'id' => $tender->egp_id
            ]);

        if (!$response->successful()) {
            Log::error("Detail API error", [
                'tender_id' => $tender->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new \Exception("Detail API error: " . $response->status());
        }

        return $response->json();
    }


    private function detectType(array $data): string
    {
        if (isset($data['reofferNo'])) {
            return 'reoffer';
        }

        if (isset($data['bidpPlanDetail'])) {
            return 'ldt';
        }

        if (isset($data['someAdbKey'])) {
            return 'adb';
        }

        return 'unknown';
    }

    private function mapDataByType(string $type, array $data): array
    {
        return match ($type) {
            'reoffer' => $this->mapReoffer($data),
            'ldt' => $this->mapLdt($data),
            'adb' => $this->mapAdb($data),
            default => throw new \Exception("Unknown detail type"),
        };
    }


    public function saveDetail(Tender $tender, array $data): void
    {
        try {
            // 🔥 ROOT DATA
            $main =
                data_get($data, 'bidoNotifyContractorM') ??
                data_get($data, 'bidoNotifyContractorP') ??
                [];
            $plan = data_get($data, 'bidpPlanDetail', []);
            $approval =
                data_get($data, 'bidoInvContractorOfflineDTO') ??
                data_get($data, 'bidInvContractorOfflineDTO') ??
                [];

            TenderDetail::updateOrCreate(
                ['tender_id' => $tender->id],
                [
                    // ========================
                    // PLAN INFO
                    // ========================
                    'plan_no' => data_get($main, 'planNo'),
                    'plan_type' => data_get($main, 'planType'),
                    'plan_name' => data_get($main, 'planName'),

                    // ========================
                    // TENDER INFO
                    // ========================
                    'tender_code' => data_get($main, 'notifyNo'),
                    'version' => data_get($main, 'notifyVersion'),

                    'publish_date' => $this->parseDate(data_get($main, 'publicDate')),

                    // ========================
                    // PROJECT
                    // ========================
                    'project_code' => data_get($main, 'projectCode'),
                    'project_name' => data_get($main, 'projectName'),

                    // ========================
                    // BID INFO
                    // ========================
                    'bid_name' => is_array(data_get($main, 'bidName'))
                        ? implode(', ', data_get($main, 'bidName'))
                        : data_get($main, 'bidName'),
                    'investor' => data_get($main, 'investorName'),

                    'fund_detail' => data_get($main, 'capitalDetail'),
                    'field' => data_get($main, 'investField'),

                    'bid_selection_form' => data_get($main, 'bidForm'),
                    'contract_type' => data_get($main, 'contractType'),

                    'is_domestic' => (int) data_get($main, 'isDomestic', 1),
                    'bid_method' => data_get($main, 'bidMode'),

                    'execution_time' => data_get($main, 'contractPeriod') . ' ' . data_get($main, 'contractPeriodUnit'),

                    'is_multi_lot' => (int) data_get($main, 'isMultiLot', 0),

                    'bid_form' => data_get($main, 'bidForm'),

                    // ========================
                    // LOCATION / TIME
                    // ========================
                    'ehsmt_issue_place' => data_get($main, 'issueLocation'),
                    'ehsdt_receive_place' => data_get($main, 'receiveLocation'),

                    'execution_place' => data_get($main, 'executionLocation'),

                    'bid_close_time' => $this->parseDate(data_get($main, 'bidCloseDate')),
                    'bid_open_time' => $this->parseDate(data_get($main, 'bidOpenDate')),

                    'bid_open_place' => data_get($main, 'bidOpenLocation'),

                    'bid_validity' => data_get($main, 'bidValidityPeriod'),

                    // ========================
                    // MONEY
                    // ========================
                    'bid_guarantee_amount' =>
                    data_get($main, 'guaranteeValue') ??
                        data_get($main, 'bidGuaranteeValue'),
                    'bid_guarantee_form' => data_get($main, 'guaranteeForm'),

                    // ========================
                    // APPROVAL
                    // ========================
                    'approval_decision_no' => data_get($approval, 'decisionNo'),
                    'approval_date' => $this->parseDate(data_get($approval, 'decisionDate')),
                    'approval_agency' => data_get($approval, 'decisionAgency'),
                    'approval_file' => data_get($approval, 'decisionFileName'),

                    // ========================
                    // DEBUG (QUAN TRỌNG)
                    // ========================
                    'raw_html' => json_encode($data, JSON_UNESCAPED_UNICODE),
                ]
            );
        } catch (\Exception $e) {
            Log::error("Save detail failed", [
                'tender_id' => $tender->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function resolveDetailApi(Tender $tender): string
    {
        $stepCode = $tender->step_code;
        $processApply = $tender->process_apply;
        $bidMode = $tender->bid_mode;

        if (str_contains($stepCode, 'reoffer')) {
            return 'online-reoffer/detail';
        }


        if ($processApply === 'KHAC') {
            return 'lcnt_tbmt_ttc_vk_adb';
        }


        if (str_contains($stepCode, 'hsmt')) {
            return 'lcnt_tbmcgtt_hsmt';
        }


        if (
            $processApply === 'LDT'
            && str_contains($stepCode, 'tbmt')
        ) {
            return 'lcnt_tbmt_ttc_ldt';
        }


        return 'lcnt_tbmt_ttc_ldt';
    }
}
