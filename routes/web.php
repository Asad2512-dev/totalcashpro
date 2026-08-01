<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\AccessRequestController;
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

Route::controller(AccessRequestController::class)->group(function (): void {
    Route::get('/request-access', 'create')->name('request-access');
    Route::post('/request-access', 'store')->name('request-access.store');
    Route::get('/request-access/thanks', 'thanks')->name('request-access.thanks');
});

/*
|--------------------------------------------------------------------------
| Auth placeholders
|--------------------------------------------------------------------------
|
| Login will be wired when the application dashboard is ready.
| Marketing CTAs for account creation go through Request Access.
|
*/
Route::redirect('/login', '/request-access')->name('login');
Route::redirect('/get-started', '/request-access')->name('register');
Route::redirect('/buy', '/request-access')->name('buy');
