<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\HomeController;
use App\Http\Controllers\Marketing\PageController as MarketingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

require __DIR__.'/kiosk.php';

Route::get('/features', [\App\Http\Controllers\Marketing\PageController::class, 'features'])->name('features');
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
    Route::redirect('/request-access', '/register')->name('request-access');
    Route::redirect('/request-demo', '/register')->name('request-demo');
    Route::redirect('/request-access/thanks', '/register')->name('request-access.thanks');
});

Route::middleware('guest')->controller(RegisterController::class)->group(function (): void {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store')->middleware('throttle:10,1')->name('register.store');
});

Route::middleware('auth')->controller(VerifyEmailController::class)->group(function (): void {
    Route::get('/email/verify', 'notice')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', 'send')->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('guest')->controller(LoginController::class)->group(function (): void {
    Route::get('/login', 'create')->name('login');
    Route::post('/login', 'store')->middleware('throttle:10,1')->name('login.attempt');
});

Route::middleware('guest')->controller(PasswordResetController::class)->group(function (): void {
    Route::get('/forgot-password', 'requestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', 'resetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->middleware('throttle:password-reset')->name('password.update');
});

Route::middleware('guest')->controller(TwoFactorController::class)->group(function (): void {
    Route::get('/two-factor-challenge', 'create')->name('two-factor.challenge');
    Route::post('/two-factor-challenge', 'store')->middleware('throttle:otp')->name('two-factor.verify');
    Route::post('/two-factor-challenge/resend', 'resend')->middleware('throttle:otp')->name('two-factor.resend');
});

Route::post('/webhooks/stripe', \App\Http\Controllers\Billing\StripeWebhookController::class)
    ->name('webhooks.stripe');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::redirect('/get-started', '/register');
Route::redirect('/buy', '/#pricing')->name('buy');

Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super_admin'])
    ->group(base_path('routes/super-admin.php'));

Route::prefix('business-admin')
    ->name('business-admin.')
    ->middleware(['auth', 'business_admin', 'org_active', 'verified'])
    ->group(function (): void {
        Route::get('/onboarding', [\App\Http\Controllers\BusinessAdmin\OnboardingController::class, 'show'])->name('onboarding');
        Route::post('/onboarding', [\App\Http\Controllers\BusinessAdmin\OnboardingController::class, 'store'])->name('onboarding.store');
        Route::post('/onboarding/skip', [\App\Http\Controllers\BusinessAdmin\OnboardingController::class, 'skip'])->name('onboarding.skip');

        Route::get('/subscription/choose-plan', [\App\Http\Controllers\BusinessAdmin\PlanSelectionController::class, 'create'])->name('subscription.choose-plan');
        Route::post('/subscription/choose-plan', [\App\Http\Controllers\BusinessAdmin\PlanSelectionController::class, 'store'])->name('subscription.choose-plan.store');
    });

Route::prefix('business-admin')
    ->name('business-admin.')
    ->middleware(['auth', 'business_admin', 'org_active', 'verified', 'plan_selected', 'onboarding_complete'])
    ->group(base_path('routes/business-admin.php'));

Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'staff', 'org_active', 'verified'])
    ->group(base_path('routes/staff.php'));