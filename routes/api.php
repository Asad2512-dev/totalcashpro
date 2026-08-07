<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/tokens', [TokenController::class, 'index'])->name('api.tokens.index');
    Route::post('/tokens', [TokenController::class, 'store'])->middleware('throttle:api-tokens')->name('api.tokens.store');
    Route::delete('/tokens/{tokenId}', [TokenController::class, 'destroy'])->name('api.tokens.destroy');
});
