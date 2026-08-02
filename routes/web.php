<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Marketing\AccessRequestController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\HomeController;
use App\Http\Controllers\Marketing\PageController as MarketingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::redirect('/features', '/#features')->name('features');
Route::redirect('/solutions', '/#solutions')->name('solutions');
Route::redirect('/pricing', '/#pricing')->name('pricing');

Route::controller(MarketingPageController::class)->group(function (): void {
    Route::get('/about', 'about')->name('about');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
});

Route::controller(ContactController::class)->group(function (): void {
    Route::get('/contact', 'create')->name('contact');
    Route::post('/contact', 'store')->name('contact.store');
});

Route::controller(AccessRequestController::class)->group(function (): void {
    Route::get('/request-access', 'create')->name('request-access');
    Route::get('/request-demo', 'create')->name('request-demo');
    Route::post('/request-access', 'store')->name('request-access.store');
    Route::get('/request-access/thanks', 'thanks')->name('request-access.thanks');
});

Route::middleware('guest')->controller(LoginController::class)->group(function (): void {
    Route::get('/login', 'create')->name('login');
    Route::post('/login', 'store')->name('login.attempt');
});

Route::middleware('guest')->controller(PasswordResetController::class)->group(function (): void {
    Route::get('/forgot-password', 'requestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->name('password.email');
    Route::get('/reset-password/{token}', 'resetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::redirect('/get-started', '/request-demo')->name('register');
Route::redirect('/buy', '/#pricing')->name('buy');

Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super_admin'])
    ->group(base_path('routes/super-admin.php'));

Route::prefix('business-admin')
    ->name('business-admin.')
    ->middleware(['auth', 'business_admin', 'org_active'])
    ->group(base_path('routes/business-admin.php'));

Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'staff', 'org_active'])
    ->group(base_path('routes/staff.php'));