<?php

declare(strict_types=1);

use App\Http\Controllers\Rider\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::post('/deliveries/{delivery}/advance', [DashboardController::class, 'advance'])->name('deliveries.advance');
Route::post('/deliveries/{delivery}/accept', [DashboardController::class, 'accept'])->name('deliveries.accept');
Route::post('/deliveries/{delivery}/reject', [DashboardController::class, 'reject'])->name('deliveries.reject');
