<?php

use App\Http\Controllers\CrawlController;
use Illuminate\Support\Facades\Route;


Route::prefix('crawl')->group(function () {

    Route::post('/full', [
        CrawlController::class,
        'full'
    ]);

    Route::post('/range', [
        CrawlController::class,
        'range'
    ]);

    Route::post('/daily', [
        CrawlController::class,
        'daily'
    ]);

    Route::get('/stats', [
        CrawlController::class,
        'stats'
    ]);

    Route::get('/history', [
        CrawlController::class,
        'history'
    ]);

    // Retry endpoints (Phase A lightweight)
    Route::get('/tasks/{task}/retry-candidates', [
        App\Http\Controllers\CrawlRetryController::class,
        'candidates'
    ]);

    Route::post('/tasks/{task}/retry', [
        App\Http\Controllers\CrawlRetryController::class,
        'retry'
    ]);
    Route::get('/tasks/{task}/zombie-check', [
        App\Http\Controllers\CrawlRetryController::class,
        'zombieCheck'
    ]);
});
