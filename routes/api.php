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
});
