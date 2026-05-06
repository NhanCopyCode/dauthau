<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\TenderDetail;
use App\Models\TenderHntdt;
use App\Models\TenderKn;
use App\Models\TenderYclr;
use App\Services\HsmtTreeService;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{

    public function index(Request $request)
    {
        $query = Tender::query();

        $query->select([
            'id',
            'name',
            'egp_id',
            'investor',
            'province',
            'bid_price',
            'public_date',
            'bid_close_date',
            'notify_no',
            'invest_field',
            'bid_form'
        ]);

        $search = trim($request->search);

        if (!empty($search)) {

            $normalized = mb_strtolower($search);
            $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);

            $keywords = array_filter(explode(' ', $normalized));

            $query->where(function ($mainQuery) use ($keywords, $normalized) {

                $mainQuery->orWhere('name', 'LIKE', "%{$normalized}%");

                foreach ($keywords as $keyword) {
                    if (strlen($keyword) < 2) continue;

                    $like = "%{$keyword}%";

                    $mainQuery->orWhere(function ($sub) use ($like) {
                        $sub->where('name', 'LIKE', $like)
                            ->orWhere('notify_no', 'LIKE', $like)
                            ->orWhere('notify_no_stand', 'LIKE', $like)
                            ->orWhere('investor', 'LIKE', $like)
                            ->orWhere('province', 'LIKE', $like)
                            ->orWhere('plan_no', 'LIKE', $like)
                            ->orWhere('bid_names', 'LIKE', $like)
                            ->orWhere('locations', 'LIKE', $like)
                            ->orWhere('invest_fields', 'LIKE', $like);
                    });
                }
            });

            $scoreParts = [];

            $scoreParts[] = "CASE WHEN name LIKE '%{$normalized}%' THEN 100 ELSE 0 END";

            foreach ($keywords as $keyword) {
                if (strlen($keyword) < 2) continue;

                $like = "%{$keyword}%";

                $scoreParts[] = "
                CASE 
                    WHEN name LIKE '{$like}' THEN 10
                    WHEN bid_names LIKE '{$like}' THEN 6
                    WHEN investor LIKE '{$like}' THEN 4
                    WHEN province LIKE '{$like}' THEN 2
                    ELSE 0
                END
            ";
            }

            $scoreSql = implode(' + ', $scoreParts);

            // 🔥 QUAN TRỌNG: dùng addSelect
            $query->addSelect(DB::raw("({$scoreSql}) as relevance_score"));

            // 🔥 ưu tiên theo score
            $query->orderByDesc('relevance_score');
        } else {
            $query->opening();

            $query->orderBy('bid_close_date', 'asc');
        }

        if ($request->province) {
            $query->where('province', $request->province);
        }

        if ($request->investor) {
            $query->where('investor', 'LIKE', "%" . trim($request->investor) . "%");
        }

        if ($request->invest_field) {
            $query->whereIn('invest_field', (array) $request->invest_field);
        }

        if ($request->price_min) {
            $query->where('bid_price', '>=', $request->price_min);
        }

        if ($request->price_max) {
            $query->where('bid_price', '<=', $request->price_max);
        }

        if ($request->public_from) {
            $query->whereDate('public_date', '>=', $request->public_from);
        }

        if ($request->public_to) {
            $query->whereDate('public_date', '<=', $request->public_to);
        }

        if ($request->close_from) {
            $query->whereDate('bid_close_date', '>=', $request->close_from);
        }

        if ($request->close_to) {
            $query->whereDate('bid_close_date', '<=', $request->close_to);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 30, 50, 100]) ? $perPage : 10;

        $tenders = $query->paginate($perPage)->withQueryString();

        $provinces = Tender::query()
            ->select('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        return view('frontend.pages.home', compact('tenders', 'provinces'));
    }

    public function show($egp_id, HsmtTreeService $treeService)
    {
        $tenderDetail = TenderDetail::with([
            'tender.hsmtChapters'
        ])
            ->whereHas('tender', function ($query) use ($egp_id) {
                $query->where('egp_id', $egp_id);
            })
            ->firstOrFail();

        $tender = $tenderDetail->tender;
        $isAgreeFrame = $tenderDetail->is_agree_frame ?? 0;

        $tree = $treeService->build(
            $tender->hsmtChapters,
            $isAgreeFrame
        );
        $notifyId = $tender->notify_id;

        $yclrs = TenderYclr::where('notify_no', $tender->notify_no)
            ->get()
            ->map(function ($item) {

                $data = is_array($item->data)
                    ? $item->data
                    : json_decode($item->data, true);

                return [
                    'version' => $data['version'] ?? $item->notify_version,

                    'req_name' => $data['header']['req_name'] ?? null,
                    'req_date' => $data['header']['req_date'] ?? null,

                    'qa_groups' => $data['qa_groups'] ?? [],
                ];
            })
            ->sortByDesc('req_date')
            ->values();
        // dd($yclrs);

        $hntdts = TenderHntdt::where('notify_no', $tender->notify_no)
            ->get()
            ->map(function ($item) {

                $data = is_array($item->data)
                    ? $item->data
                    : json_decode($item->data, true);

                return [
                    'version' => $data['version'] ?? null,
                    'rows' => $data['rows'] ?? []
                ];
            });

        // dd($hntdts);

        $knData = TenderKn::where('notify_no', $tender->notify_no)
            ->first();

        $knData = $knData
            ? collect(is_array($knData->data) ? $knData->data : json_decode($knData->data, true))
            ->map(function ($versionBlock) {

                return [
                    'version' => $versionBlock['notifyVersion'] ?? '00',
                    'items' => collect($versionBlock['biduPetitionContractorDTOList'] ?? [])
                        ->map(function ($item) {

                            $content = json_decode($item['content'] ?? '[]', true);
                            $content = $content[0] ?? [];

                            return [
                                'req_no' => $item['reqNo'] ?? null,
                                'req_name' => $item['reqName'] ?? null,

                                'req_date' => $item['reqDate'] ?? null,
                                'res_date' => $item['resDate'] ?? null,

                                'req_content' => $content['reqContent'] ?? null,
                                'res_content' => $content['resContent'] ?? null,

                                'req_file_name' => $content['reqFileName'] ?? null,
                                'req_file_id' => $content['reqFileId'] ?? null,

                                'res_file_name' => $content['resFileName'] ?? null,
                                'res_file_id' => $content['resFileId'] ?? null,
                            ];
                        })
                ];
            })
            : collect();
        // dd($knData);

        return view('frontend.pages.tender-detail', [
            'tenderDetail' => $tenderDetail,
            'tender' => $tender,
            'tree' => $tree,
            'stepCode' => $tender->step_code,
            'hasCgtt' => in_array($tenderDetail->bid_form, ['CGTTRG', 'CGTT']),
            'hasContract' => false,
            'notifyId' => $notifyId,
            'yclrs' => $yclrs,
            'hntdts' => $hntdts,
            'knData' => $knData,
        ]);
    }
}
