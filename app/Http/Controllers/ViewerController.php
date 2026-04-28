<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ViewerController extends Controller
{
    public function render()
    {
        $response = Http::timeout(60)->post(
            'http://localhost:3000/api/render',
            [
                'formCode' => request('formCode'),
                'id' => request('id')
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Render failed'
            ], 500);
        }

        return response()->json($response->json());
    }
}