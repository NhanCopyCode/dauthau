<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlanController extends Controller
{
    //
    public function show($id)
    {
        $token = config('crawler.token');
        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/expose/lcnt/bid-po-bidp-plan-project-view/get-by-id?token={$token}";
        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                "accept" => "application/json",
                "content-type" => "application/json",
            ])->withOptions([
                'verify' => false
            ])
            ->post(
                $url,
                [
                    'id' => $id
                ]
            );


        if (!$response->successful()) {
            abort(404, 'Không lấy được dữ liệu KHLCNT');
        }

        $data = $response->json();

        $plan = data_get($data, 'bidPoBidpPlanProjectDetailView');
        $packages = data_get($data, 'bidpPlanDetailToProjectList', []);
        $project = data_get($data, 'project');

        $tender = Tender::with('detail')->where('plan_no', data_get($plan, 'planNo'))->first();

        return view('frontend.pages.khlcnt', compact(
            'plan',
            'packages',
            'project',
            'tender'
        ));
    }

    

    public function showDetail(Request $request, $id)
    {
        $token = config('crawler.token');

        // ===== 1. LẤY plan_id từ query =====
        $planId = $request->query('plan_id');

        // ===== 2. CALL API DETAIL =====
        $detailUrl = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/lcnt/bid-po-bidp-plan-project-view/get-bidp-plan-detail-by-id?token={$token}";

        $detailResponse = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                "accept" => "application/json",
                "content-type" => "application/json",
            ])
            ->withOptions(['verify' => false])
            ->post($detailUrl, [
                'id' => $id
            ]);

        if (!$detailResponse->successful()) {
            abort(404, 'Không lấy được chi tiết gói thầu');
        }

        $detail = $detailResponse->json();
        // ===== 3. CALL API PLAN (QUAN TRỌNG) =====
        $plan = null;

        if ($planId) {
            $planUrl = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/expose/lcnt/bid-po-bidp-plan-project-view/get-by-id?token={$token}";

            $planResponse = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders([
                    "accept" => "application/json",
                    "content-type" => "application/json",
                ])
                ->withOptions(['verify' => false])
                ->post($planUrl, [
                    'id' => $planId
                ]);

            if ($planResponse->successful()) {
                $planData = $planResponse->json();
                $plan = data_get($planData, 'bidPoBidpPlanProjectDetailView');
            }
        }

        $locations = data_get($detail, 'bidLocation', []);
        $lots = data_get($planResponse, 'bidpPlanDetailToProjectList', []);
        $contractors_list = collect(data_get($detail, 'resultDTO.lotResultDTO', []))
            ->pluck('contractorList')   // lấy mảng contractorList của từng lot
            ->filter()                  // loại null
            ->flatten(1)                // gộp lại thành 1 mảng
            ->values()
            ->all();

            
        $tender = Tender::with('detail')
            ->where('plan_no', data_get($detail, 'planNo'))
            ->first();

        return view('frontend.pages.khlcnt-detail', compact(
            'detail',
            'locations',
            'lots',
            'tender',
            'plan',
            'contractors_list'
        ));
    }
}
