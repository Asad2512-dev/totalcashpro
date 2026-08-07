<?php

declare(strict_types=1);

use App\Http\Controllers\Kiosk\SmartKioskController;
use Illuminate\Support\Facades\Route;

Route::prefix('kiosk')
    ->name('kiosk.')
    ->group(function (): void {
        Route::get('/{token}', [SmartKioskController::class, 'show'])->name('show');
        Route::post('/{token}/start', [SmartKioskController::class, 'start'])
            ->middleware('throttle:10,1')
            ->name('start');
        Route::post('/{token}/pin', [SmartKioskController::class, 'pin'])
            ->middleware('throttle:30,1')
            ->name('pin');
        Route::post('/{token}/exit', [SmartKioskController::class, 'exit'])
            ->middleware('throttle:10,1')
            ->name('exit');
    });
