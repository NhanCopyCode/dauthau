<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderKn;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class KnService
{
    public function handle(Tender $tender): void
    {
        $token = config('crawler.token');
        $url = config('crawler.kn_url') . '?token=' . $token;

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withOptions([
                'verify' => false
            ])
            ->post($url, [
                'notifyNo' => $tender->notify_no,
                'processApply' => $tender->process_apply ?? 'LDT'
            ]);

        if (!$response->successful()) {
            Log::error('KN API failed', [
                'url' => $url,
                'tender_id' => $tender->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return;
        }

        $data = $response->json();

        $versions = data_get($data, 'biduPetitionContractorVersionDTOList', []);

        $items = collect($versions)
            ->flatMap(fn($v) => $v['biduPetitionContractorDTOList'] ?? []);

        if ($items->isEmpty()) {
            Log::warning('KN EMPTY AFTER FLATTEN', [
                'tender_id' => $tender->id,
                'notify_no' => $tender->notify_no,
                'response' => $data
            ]);
            return;
        }

        $latestReqDate = $items
            ->pluck('reqDate')
            ->filter()
            ->max();

        $latestResDate = $items
            ->pluck('resDate')
            ->filter()
            ->max();

        try {
            TenderKn::updateOrCreate(
                ['notify_no' => $tender->notify_no],
                [
                    'kn_count' => $items->count(),

                    'latest_req_date' => $latestReqDate
                        ? Carbon::parse($latestReqDate)
                        : null,

                    'latest_res_date' => $latestResDate
                        ? Carbon::parse($latestResDate)
                        : null,

                    'data' => $versions,
                    'raw' => $data
                ]
            );
        } catch (\Throwable $e) {
            Log::error('KN SAVE FAILED', [
                'tender_id' => $tender->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
