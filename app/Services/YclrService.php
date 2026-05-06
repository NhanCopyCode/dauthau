<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderYclr;
use Illuminate\Support\Facades\Http;

class YclrService
{
    public function handle(Tender $tender): void
    {
        $url = config('crawler.yclr_url') . '?token=' . config('crawler.token');
        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withOptions(['verify' => false])
            ->post($url, [
                'token' => config('crawler.token'),
                'notifyNo' => $tender->notify_no,
                'processApply' => $tender->process_apply ?? 'LDT',
            ]);

        if (!$response->successful()) {
            return;
        }

        $versions = data_get($response->json(), 'biduClarifyReqInvAndContentViewVersionDTOList', []);

        foreach ($versions as $version) {
            $this->store($tender, $version);
        }
    }

    private function store(Tender $tender, array $version): void
    {
        $items = data_get($version, 'biduClarifyReqInvAndContentViewList', []);

        $qaGroups = collect($items)->map(function ($item, $index) {

            $req = $this->decode($item['clarifyReqContent'])[0] ?? [];
            $res = $this->decode($item['clarifyResContent'])[0] ?? [];

            return [
                'title' => 'Nội dung hỏi đáp ' . ($index + 1),

                'header' => [
                    'req_name' => $item['reqName'] ?? null,
                    'req_date' => $item['reqDate'] ?? null,
                ],

                'items' => [
                    [
                        'subject' => $req['subjectName'] ?? null,
                        'question' => $req['question'] ?? null,
                        'answer' => $res['response'] ?? null,

                        'files' => [
                            'req_file_name' => $item['clarify_file_name'] ?? null,
                            'req_file_id' => $item['clarify_file_id'] ?? null,
                            'res_file_name' => $item['clarifyResFileName'] ?? null,
                            'res_file_id' => $item['clarifyResFileId'] ?? null,
                        ],

                        'dates' => [
                            'sign_req_date' => $item['signReqDate'] ?? null,
                            'sign_res_date' => $item['signResDate'] ?? null,
                        ],
                    ]
                ]
            ];
        })->values()->all();

        TenderYclr::updateOrCreate(
            [
                'notify_no' => $tender->notify_no,
                'notify_version' => $version['notifyVersion'],
            ],
            [
                'data' => [
                    'version' => $version['notifyVersion'],
                    'qa_groups' => $qaGroups,
                ],
                'raw' => $version,
            ]
        );
    }

    private function decode(?string $json): array
    {
        return $json ? json_decode($json, true) ?: [] : [];
    }
}
