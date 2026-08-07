<?php

declare(strict_types=1);

use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\AvailabilityController;
use App\Http\Controllers\Staff\CashUpController;
use App\Http\Controllers\Staff\ClockController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\HoursController;
use App\Http\Controllers\Staff\LeaveController;
use App\Http\Controllers\Staff\NotificationController;
use App\Http\Controllers\Staff\ProfileController;
use App\Http\Controllers\Staff\ShiftController;
use App\Http\Controllers\Staff\ShiftSwapController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::middleware('plan_feature:attendance')->group(function (): void {
    Route::get('/clock', [ClockController::class, 'index'])->name('clock');
    Route::get('/clock/status', [ClockController::class, 'status'])->name('clock.status');
    Route::post('/clock/action', [ClockController::class, 'action'])->middleware('throttle:30,1')->name('clock.action');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/hours', [HoursController::class, 'index'])->name('hours');
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability');
    Route::put('/availability', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
});

Route::middleware('plan_feature:cash_up')->group(function (): void {
    Route::get('/cash-up', [CashUpController::class, 'index'])->name('cash-up');
    Route::post('/cash-up', [CashUpController::class, 'store'])->name('cash-up.store');
    Route::post('/cash-up/deductions', [CashUpController::class, 'storeDeductions'])->name('cash-up.deductions');
});

Route::middleware('plan_feature:rota')->group(function (): void {
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift');
    Route::get('/shift-swap', [ShiftSwapController::class, 'index'])->name('shift-swap');
    Route::post('/shift-swap', [ShiftSwapController::class, 'store'])->name('shift-swap.store');
});

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::prefix('security')->name('security.')->group(base_path('routes/security.php'));
