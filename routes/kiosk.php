<?php

declare(strict_types=1);

use App\Http\Controllers\Kiosk\KioskV2Controller;
use App\Http\Controllers\Kiosk\SmartKioskController;
use Illuminate\Support\Facades\Route;

Route::prefix('kiosk')
    ->name('kiosk.')
    ->group(function (): void {
        Route::get('/', [KioskV2Controller::class, 'show'])->name('index');
        Route::post('/login', [KioskV2Controller::class, 'login'])
            ->middleware('throttle:10,1')
            ->name('v2.login');
        Route::post('/select-branch', [KioskV2Controller::class, 'selectBranch'])
            ->middleware('throttle:30,1')
            ->name('v2.select-branch');
        Route::post('/settings', [KioskV2Controller::class, 'updateSettings'])
            ->middleware('throttle:30,1')
            ->name('v2.settings');
        Route::post('/pin', [KioskV2Controller::class, 'pin'])
            ->middleware('throttle:30,1')
            ->name('v2.pin');
        Route::post('/action', [KioskV2Controller::class, 'action'])
            ->middleware('throttle:60,1')
            ->name('v2.action');
        Route::get('/attendance', [KioskV2Controller::class, 'attendance'])
            ->middleware('throttle:120,1')
            ->name('v2.attendance');
        Route::post('/logout', [KioskV2Controller::class, 'logout'])
            ->middleware('throttle:10,1')
            ->name('v2.logout');
        Route::post('/change-branch', [KioskV2Controller::class, 'changeBranch'])
            ->middleware('throttle:30,1')
            ->name('v2.change-branch');

        // Legacy token-based kiosk (backward compatibility)
        Route::get('/{token}', [SmartKioskController::class, 'show'])->name('show');
        Route::post('/{token}/start', [SmartKioskController::class, 'start'])
            ->middleware('throttle:10,1')
            ->name('start');
        Route::post('/{token}/pin', [SmartKioskController::class, 'pin'])
            ->middleware('throttle:30,1')
            ->name('pin');
        Route::post('/{token}/action', [SmartKioskController::class, 'action'])
            ->middleware('throttle:60,1')
            ->name('action');
        Route::post('/{token}/sync', [SmartKioskController::class, 'sync'])
            ->middleware('throttle:120,1')
            ->name('sync');
        Route::post('/{token}/exit', [SmartKioskController::class, 'exit'])
            ->middleware('throttle:10,1')
            ->name('exit');
    });
