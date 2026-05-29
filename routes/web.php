<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HsmtController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TBMTController;
use App\Http\Controllers\TenderExportController;

use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/tenders', [FrontendController::class, 'index'])->name('tenders.index');
Route::get('/show-tender-page', [FrontendController::class, 'showTenderPage'])->name('tenders.page');

Route::post('/download-hsmt', [HsmtController::class, 'downloadHsmt']);

Route::get('/tenders/{egp_id}', [FrontendController::class, 'show'])->name('tenders.show');
Route::get('/khlcnt/{id}', [PlanController::class, 'show'])->name('khlcnt.show');
Route::get('/khlcnt-detail/{id}', [PlanController::class, 'showDetail'])->name('khlcnt.detail');

Route::get('/tenders/{id}/export-excel', [TenderExportController::class, 'export'])
    ->name('tenders.export.excel');
Route::get('/tenders/{egp_id}/download', [TBMTController::class, 'download'])
    ->name('tenders.download');

// Crawl task logs
Route::get('/crawl-tasks/{task}/logs', [\App\Http\Controllers\CrawlTaskLogController::class, 'logs']);
Route::get('/crawl-tasks/{task}/detail', [\App\Http\Controllers\CrawlTaskDetailController::class, 'show']);

// Notifications
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
