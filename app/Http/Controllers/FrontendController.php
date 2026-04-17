<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\TenderDetail;

class FrontendController extends Controller
{
   public function index(Request $request)
    {
        $query = Tender::query();

        $search = trim($request->search);

        if (!empty($search)) {
            $normalized = mb_strtolower($search);
            $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);

            $keywords = array_filter(explode(' ', $normalized));

            $query->where(function ($mainQuery) use ($keywords) {
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
        } else {
            $query->opening();
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

        $query->orderBy('bid_close_date', 'asc');

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
    public function show($egp_id)
    {
        $tender = TenderDetail::with('tender')
            ->whereHas('tender', function ($query) use ($egp_id) {
                $query->where('egp_id', $egp_id);
            })
            ->firstOrFail();

        return view('frontend.pages.tender-detail', compact('tender'));
    }
}
