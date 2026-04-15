<?php

namespace App\Http\Controllers;

use App\Models\TenderDetail;
use App\Services\ScopeTableService;
use App\Exports\ScopeTableExport;
use Maatwebsite\Excel\Facades\Excel;

class TenderExportController extends Controller
{
    public function export($id, ScopeTableService $service)
    {
        $detail = TenderDetail::findOrFail($id);

        $scope = $detail->scope_table;

        [$headings, $rows] = $service->transform($scope);

        $rawName = $detail->scope_chapter_name . '_' . $detail->notify_no;

        $fileName = $this->sanitizeFileName($rawName) . '.xlsx';

        return Excel::download(
            new ScopeTableExport(
                $rows,
                $headings,
                $detail->scope_chapter_name 
            ),
            $fileName
        );
    }

    private function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[\\\\\\/*?:"<>|]/', '', $name);

        $name = str_replace(["\n", "\r"], ' ', $name);

        $name = trim($name);

        return $name;
    }
}
