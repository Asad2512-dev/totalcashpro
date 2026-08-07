<?php

declare(strict_types=1);

use App\Http\Controllers\Security\AccountSecurityController;
use Illuminate\Support\Facades\Route;

Route::controller(AccountSecurityController::class)->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::post('/two-factor/enable', 'enableTwoFactor')->middleware('throttle:otp')->name('two-factor.enable');
    Route::post('/two-factor/confirm', 'confirmTwoFactor')->middleware('throttle:otp')->name('two-factor.confirm');
    Route::post('/two-factor/disable', 'disableTwoFactor')->name('two-factor.disable');
    Route::post('/password', 'updatePassword')->name('password.update');
    Route::post('/email/request', 'requestEmailChange')->middleware('throttle:otp')->name('email.request');
    Route::post('/email/confirm', 'confirmEmailChange')->middleware('throttle:otp')->name('email.confirm');
    Route::post('/notifications', 'updateNotificationPreferences')->name('notifications.update');
    Route::post('/devices/{device}/trust', 'trustDevice')->name('devices.trust');
    Route::post('/devices/{device}/logout', 'logoutDevice')->name('devices.logout');
    Route::post('/devices/logout-all', 'logoutAllDevices')->name('devices.logout-all');
});
