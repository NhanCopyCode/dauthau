<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('tenders.index');
Route::get('/tenders/{id}', [FrontendController::class, 'show'])->name('tenders.show');