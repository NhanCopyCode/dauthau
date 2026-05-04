<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HsmtController extends Controller
{
    public function downloadHsmt(Request $request)
    {
        $url = $request->input('url');
        $param = $request->input('param');

        // 🔒 validate
        if (!$url || !$param) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        // 🚀 call NodeJS
        $response = Http::timeout(120)->post('http://localhost:3000/download', [
            'url' => $url,
            'param' => $param
        ]);

        Log::info('Node response', $response->json());

        if (!$response->successful()) {
            return response()->json(['error' => 'Node error'], 500);
        }

        $data = $response->json();

        if (empty($data['path'])) {
            return response()->json(['error' => 'No file'], 500);
        }

        $filePath = $data['path'];

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
