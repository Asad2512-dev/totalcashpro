<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/tokens', [TokenController::class, 'index'])->name('api.tokens.index');
    Route::post('/tokens', [TokenController::class, 'store'])->middleware('throttle:api-tokens')->name('api.tokens.store');
    Route::delete('/tokens/{tokenId}', [TokenController::class, 'destroy'])->name('api.tokens.destroy');
});

Route::prefix('kiosk')->name('api.kiosk.')->group(function (): void {
    Route::post('/authenticate', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'authenticate'])
        ->middleware('throttle:10,1')
        ->name('authenticate');
    Route::post('/select-branch', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'selectBranch'])
        ->middleware('throttle:30,1')
        ->name('select-branch');
    Route::get('/config', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'config'])
        ->middleware('throttle:120,1')
        ->name('config');
    Route::post('/pin', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'pin'])
        ->middleware('throttle:30,1')
        ->name('pin');
    Route::post('/action', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'action'])
        ->middleware('throttle:60,1')
        ->name('action');
    Route::get('/attendance', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'attendance'])
        ->middleware('throttle:120,1')
        ->name('attendance');
    Route::post('/logout', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'logout'])
        ->middleware('throttle:10,1')
        ->name('logout');
    Route::post('/revoke', [\App\Http\Controllers\Api\KioskV2ApiController::class, 'revoke'])
        ->middleware(['auth:sanctum', 'throttle:30,1'])
        ->name('revoke');
});
