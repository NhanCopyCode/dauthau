<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenderDetailCrawlerService
{
    public function handle(Tender $tender): void
    {
        try {
            $type = $this->detectTypeFromTender($tender);
            Log::info('Detected type: ' . $type, ['tender_id' => $tender->id]);
            if ($type === 'adb') {
                $data = $this->fetchAbd($tender);
                $mapped = $this->handleAbd($data);
            } else {
                $ldtData = $this->fetchLdt($tender);

                Log::info("LDT RAW", [
                    'tender_id' => $tender->id,
                    'data' => $ldtData
                ]);
                if ($this->isValidLdtData($ldtData)) {
                    $mapped = $this->handleLdt($ldtData);
                } else {
                    Log::warning("LDT data invalid → fallback reoffer", [
                        'tender_id' => $tender->id
                    ]);

                    $reofferData = $this->fetchReofferOnline($tender);
                    $hsmtData = $this->fetchHSMT($tender);

                    if (empty($reofferData)) {
                        Log::error("Reoffer API returned empty", [
                            'tender_id' => $tender->id
                        ]);
                        return;
                    }

                    $mapped = $this->handleReoffer($reofferData, $hsmtData);
                }
            }

            if (empty($mapped) || empty($mapped['notify_no'])) {
                Log::warning("Mapped data empty or invalid", [
                    'tender_id' => $tender->id
                ]);
                return;
            }

            TenderDetail::updateOrCreate(
                ['tender_id' => $tender->id],
                $this->mapToModel($mapped, $tender)
            );
            Log::info("DETAIL SAVED SUCCESS", [
                'tender_id' => $tender->id,
                'notify_no' => $mapped['notify_no'] ?? null,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            Log::error("Crawler handle failed", [
                'tender_id' => $tender->id,
                'error' => $e->getMessage(),
            ]);
        }
    }


    private function fetchLdt(Tender $tender): array
    {
        return $this->callApi('ldt', $tender->egp_id, $tender->id);
    }

    private function fetchAbd(Tender $tender): array
    {
        return $this->callApi('adb', $tender->egp_id, $tender->id);
    }

    private function fetchReofferOnline(Tender $tender): array
    {
        return $this->callApi('reoffer_online', $tender->egp_id, $tender->id);
    }

    private function fetchHSMT(Tender $tender): array
    {
        return $this->callApi('hsmt', $tender->egp_id, $tender->id);
    }

    private function callApi(string $type, string $id, int $tenderId): array
    {
        $endpoint = config("crawler.detail_apis.$type");
        $token = config('crawler.token');

        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/{$endpoint}?token={$token}";

        $payload = $this->buildPayloadByType($type, $id);

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
                Log::error("API error", [
                    'tender_id' => $tenderId,
                    'type' => $type,
                    'status' => $response->status(),
                    'payload' => $payload
                ]);

                return [];
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("API exception", [
                'tender_id' => $tenderId,
                'type' => $type,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [];
        }
    }

    private function buildPayloadByType(string $type, string $id): array
    {
        switch ($type) {
            case 'hsmt':
                return [
                    'id' => $id,
                    'processApply' => 'LDT'
                ];

            default:
                return [
                    'id' => $id
                ];
        }
    }


    private function isValidLdtData(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $main =
            data_get($data, 'bidoNotifyContractorM') ??
            data_get($data, 'bidNoContractorResponse.bidNotification');

        if (!empty($main)) {
            return true;
        }

        return !empty(data_get($data, 'notifyNo'));
    }


    private function handleLdt(array $data): array
    {
        $source1 = data_get($data, 'bidoNotifyContractorM', []);
        $source2 = data_get($data, 'bidNoContractorResponse.bidNotification', []);

        $main = array_replace_recursive($source1, $source2);

        $plan = data_get($data, 'bidpPlanDetail', []);
        $approval = data_get($data, 'bidInvContractorOfflineDTO', []);

        return $this->buildCommonData($main, $plan, $approval);
    }

    private function handleAbd(array $data): array
    {
        $main =
            data_get($data, 'bidoNotifyContractorP')
            ?? data_get($data, 'bidoNotifyContractorP')
            ?? [];

        $plan = data_get($data, 'bidpPlanDetail', []);
        $approval = data_get($data, 'bidInvContractorOfflineDTO', []);

        return $this->buildCommonData($main, $plan, $approval);
    }


    private function handleReoffer(array $data, array $hsmtData): array
    {
        $main = $data;
        $plan = data_get($data, 'bidDetail', []);
        $approval = [];

        $lotRaw = data_get($data, 'bidoListContractorReofferPassedDTOList', []);
        $lotTable = $this->buildLotTable($lotRaw);

        $hsmtParsed = $this->parseHsmt($hsmtData);
        $scopeTree = $this->buildScopeTree($hsmtParsed['rows']);

        $scopeTable = [
            'columns' => $hsmtParsed['columns'],
            'rows' => $scopeTree
        ];

        return array_merge(
            $this->buildCommonData($main, $plan, $approval),
            [
                'lot_table' => $lotTable,
                'scope_table' => $scopeTable
            ]
        );
    }

    private function buildScopeTree(array $rows): array
    {
        $map = [];
        $tree = [];

        foreach ($rows as $row) {
            $row['children'] = [];
            $map[$row['id']] = $row;
        }

        foreach ($map as $id => &$node) {
            $parent = $node['parent'] ?? 0;

            if ($parent == 0) {
                $tree[] = &$node;
            } else {
                if (isset($map[$parent])) {
                    $map[$parent]['children'][] = &$node;
                }
            }
        }

        return array_values($tree);
    }

    private function parseHsmt(array $data): array
    {
        $formContentRaw = data_get($data, 'bidoInvBiddingDTO.0.formContent');
        $formValueRaw = data_get($data, 'bidoInvBiddingDTO.0.formValue');
        if (!$formContentRaw || !$formValueRaw) {
            return ['columns' => [], 'rows' => []];
        }

        $formContent = json_decode($formContentRaw, true);
        $formValue = json_decode($formValueRaw, true);

        $columns = collect($formContent)->map(function ($col) {
            return [
                'key' => $col['columnName'],
                'title' => $col['columnTitle'],
                'type' => $col['columnType'] ?? 'text'
            ];
        })->values()->toArray();

        $rows = data_get($formValue, 'Table', []);

        return [
            'columns' => $columns,
            'rows' => $rows
        ];
    }

    private function buildLotTable(array $list): array
    {
        return [
            'columns' => [
                ['key' => 'lot_no', 'title' => 'Mã lô'],
                ['key' => 'lot_name', 'title' => 'Tên lô'],
                ['key' => 'price_init', 'title' => 'Giá trần'],
                ['key' => 'price_step', 'title' => 'Bước giá'],
            ],
            'rows' => collect($list)->map(function ($item) {
                return [
                    'lot_no' => data_get($item, 'lotNo'),
                    'lot_name' => data_get($item, 'lotName'),
                    'price_init' => $this->toDecimal(data_get($item, 'priceInit')),
                    'price_step' => $this->toDecimal(data_get($item, 'priceStep')),
                ];
            })->values()->toArray()
        ];
    }

    private function buildCommonData(array $main, array $plan, array $approval): array
    {
        return array_merge(
            $this->mapCoreFields($main, $plan),
            $this->enrichExtraFields($main, $plan, $approval)
        );
    }

    private function mapCoreFields(array $main, array $plan): array
    {
        return [
            'notify_no' => data_get($main, 'notifyNo'),
            'notify_version' => data_get($main, 'notifyVersion'),
            'plan_no' => data_get($main, 'planNo') ?? data_get($plan, 'planNo'),

            'public_date' => $this->parseDate(data_get($main, 'publicDate')),
            'plan_type' => data_get($main, 'planType'),
            'plan_name' => data_get($main, 'pName') ?? data_get($main, 'planName') ?? data_get($plan, 'planName'),

            'bid_name' => data_get($main, 'bidName'),
            'investor_name' => data_get($main, 'investorName') ?? data_get($main, 'procuringEntityName'),

            'capital_detail' => data_get($main, 'capitalDetail') ?? data_get($plan, 'capitalDetail'),
        ];
    }

    private function enrichExtraFields(array $main, array $plan, array $approval): array
    {
        return [
            'invest_field' => data_get($main, 'investField'),
            'bid_form' => data_get($main, 'bidForm'),
            'contract_type' => data_get($main, 'contractType') ?? data_get($main, 'cType'),

            'is_domestic' => $this->toBool(data_get($main, 'isDomestic')),
            'bid_mode' => data_get($main, 'bidMode'),
            'contract_period' => data_get($main, 'contractPeriod')
                ?? data_get($plan, 'cperiod'),
            'contract_period_unit' => data_get($main, 'contractPeriodUnit') ?? data_get($main, 'cPeriodUnit')
                ?? data_get($plan, 'cperiodUnit'),

            'is_multi_lot' => $this->toBool(
                data_get($main, 'isMultiLot')
                    ?? data_get($plan, 'isMultiLot'),
            ),
            'lot_count' => $this->resolveLotCount($main, $plan),
            'ceiling_price' => data_get($main, 'priceInit'),
            'price_step' => data_get($main, 'priceStep'),
            'bid_validity_period_reoffer' => data_get($main, 'bidValidityPeriod'),
            'bid_validity_period_unit_reoffer' => 'D', 

            'is_online_bidding' => $this->toBool(data_get($main, 'isInternet')),
            'issue_location' => data_get($main, 'issueLocation'),
            'receive_location' => data_get($main, 'receiveLocation'),
            'execution_location' => data_get($main, 'executionLocation'),

            'reoffer_start_time' => $this->parseDate(data_get($main, 'reofferCloseDate')),
            'reoffer_end_time'   => $this->parseDate(data_get($main, 'reofferOpenDate')),

            'bid_close_date' => $this->parseDate(data_get($main, 'bidCloseDate')),
            'bid_open_date' => $this->parseDate(data_get($main, 'bidOpenDate')),
            'bid_open_location' => data_get($main, 'bidOpenLocation'),
            'bid_validity_period' => data_get($main, 'bidValidityPeriod'),
            'bid_validity_period_unit' => data_get($main, 'bidValidityPeriodUnit') ?? 'D',

            'bid_guarantee_amount' => $this->toDecimal(data_get($main, 'guaranteeValue') ?? data_get($main, 'bidGuaranteeValue')),
            'bid_guarantee_form' => data_get($main, 'guaranteeForm') ?? data_get($main, 'bidGuaranteeForm'),
            'bid_submission_fee' => $this->calculateBidSubmissionFee($main),

            'approval_decision_number' => data_get($approval, 'decisionNo'),
            'approval_decision_date' => $this->parseDate(data_get($approval, 'decisionDate')),
            'approval_agency' => data_get($approval, 'decisionAgency'),
            'approval_file_name' => data_get($approval, 'decisionFileName'),
            'modification_file_name' => data_get($approval, 'otherFileName'),

            'raw_json' => [
                'main' => $main,
                'plan' => $plan,
                'approval' => $approval
            ]


        ];
    }

    private function resolveLotCount(array $main, array $plan): ?int
    {
        $lotList = data_get($main, 'lotDTOList');

        if (is_array($lotList) && count($lotList)) {
            return count($lotList);
        }

        $text = data_get($plan, 'generalTasks', '');

        if (preg_match('/\((\d+)\s*phần\/lô\)/u', $text, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function calculateBidSubmissionFee(array $main): ?int
    {
        $bidForm = data_get($main, 'bidForm');
        $isInternet = (int) data_get($main, 'isInternet');
        $investField = data_get($main, 'investField');

        if ($investField === 'TV' && $bidForm === 'TVCN') {
            return null;
        }

        if ($isInternet !== 1) {
            return null;
        }

        $feeMap = [
            'DTRR' => 330000,
            'DTHC' => 330000,
            'MSTT' => 330000,
            'CHCT' => 220000,
            'CHCTRG' => 220000,
        ];

        return $feeMap[$bidForm] ?? null;
    }
    private function mapToModel(array $data, Tender $tender): array
    {
        return array_merge($data, [
            'tender_id' => $tender->id,

            'lot_table' => isset($data['lot_table'])
                ? $data['lot_table']
                : null,

            'scope_table' => isset($data['scope_table'])
                ? $data['scope_table']
                : null,
        ]);
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }

    private function toBool($value): ?int
    {
        return is_null($value) ? null : (int) $value;
    }

    private function toDecimal($value): ?float
    {
        return is_null($value) ? null : (float) $value;
    }

    private function detectTypeFromTender(Tender $tender): string
    {
        $type = strtolower($tender->process_apply ?? 'ldt');

        if ($type === 'khac') {
            return 'adb';
        }

        return $type;
    }
}
