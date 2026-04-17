<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Barryvdh\DomPDF\Facade\Pdf;

class TBMTController extends Controller
{
    public function download($egp_id)
    {
        $tender = Tender::with('detail')
            ->where('egp_id', $egp_id)
            ->firstOrFail();


        $pdf = Pdf::loadView('pdf.tbmt', [
            'tender' => $tender,
            'detail' => $tender->detail,
        ])->setPaper('a4');

        return $pdf->download("Thông báo mời thầu - {$tender->egp_id}.pdf");
    }
}
