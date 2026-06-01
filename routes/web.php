<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HsmtController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TBMTController;
use App\Http\Controllers\TenderExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Frontend Routes (no authentication required)
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Guest Routes (only non-authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Post-login landing — redirects based on role
    Route::get('/auth/home', function () {
        /** @var \App\Models\User $user */
        $user = Illuminate\Support\Facades\Auth::user();

        if ($user->hasAnyRole(['admin', 'operator'])) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // viewer (or unknown role) — show permission info with logout button
        return view('auth.home');
    })->name('auth.home');

    // Crawl operations — require admin or operator
    Route::middleware('role:admin,operator')->group(function () {
        Route::post('/crawl/full', [\App\Http\Controllers\CrawlController::class, 'full'])
            ->name('crawl.full');
        Route::post('/crawl/daily', [\App\Http\Controllers\CrawlController::class, 'daily'])
            ->name('crawl.daily');
        Route::post('/crawl/range', [\App\Http\Controllers\CrawlController::class, 'range'])
            ->name('crawl.range');
        Route::post('/crawl/{task}/retry', [\App\Http\Controllers\CrawlRetryController::class, 'retry'])
            ->name('crawl.retry');
        Route::get('/crawl/{task}/retry/candidates', [\App\Http\Controllers\CrawlRetryController::class, 'candidates'])
            ->name('crawl.retry.candidates');
    });

    // Admin Dashboard — admin & operator only
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('admin.dashboard');
    });

    // Crawl monitoring — admin and operator can view
    Route::get('/crawl-tasks/{task}/logs', [\App\Http\Controllers\CrawlTaskLogController::class, 'logs'])
        ->name('crawl-tasks.logs');
    Route::get('/crawl-tasks/{task}/detail', [\App\Http\Controllers\CrawlTaskDetailController::class, 'show'])
        ->name('crawl-tasks.detail');

    // Notifications — all authenticated users
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Placeholder for future admin-only features (settings, user management, etc.)
    });
});
