<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TBMTController;
use App\Http\Controllers\TenderExportController;

use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/tenders', [FrontendController::class, 'index'])->name('tenders.index');

Route::get('/tenders/{egp_id}', [FrontendController::class, 'show'])->name('tenders.show');
Route::get('/khlcnt/{id}', [PlanController::class, 'show'])->name('khlcnt.show');
Route::get('/khlcnt-detail/{id}', [PlanController::class, 'showDetail'])->name('khlcnt.detail');

Route::get('/tenders/{id}/export-excel', [TenderExportController::class, 'export'])
    ->name('tenders.export.excel');
Route::get('/tenders/{egp_id}/download', [TBMTController::class, 'download'])
    ->name('tenders.download');