<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\TenderExportController;

use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('tenders.index');
Route::get('/tenders/{id}', [FrontendController::class, 'show'])->name('tenders.show');


Route::get('/tenders/{id}/export-excel', [TenderExportController::class, 'export'])
    ->name('tenders.export.excel');