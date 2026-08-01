<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\HomeController;
use App\Http\Controllers\Marketing\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::controller(PageController::class)->group(function (): void {
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
});

/*
|--------------------------------------------------------------------------
| Auth placeholders (Phase 2)
|--------------------------------------------------------------------------
|
| Authentication is intentionally not implemented in Phase 1.
| These named routes keep marketing CTAs stable for later wiring.
|
*/
Route::redirect('/login', '/#buy')->name('login');
Route::redirect('/get-started', '/#buy')->name('register');
Route::redirect('/buy', '/#buy')->name('buy');
