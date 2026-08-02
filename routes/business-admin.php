<?php

declare(strict_types=1);

use App\Http\Controllers\BusinessAdmin\AttendanceController;
use App\Http\Controllers\BusinessAdmin\BranchController;
use App\Http\Controllers\BusinessAdmin\BranchManageController;
use App\Http\Controllers\BusinessAdmin\CashHistoryController;
use App\Http\Controllers\BusinessAdmin\CashUpController;
use App\Http\Controllers\BusinessAdmin\ClockInController;
use App\Http\Controllers\BusinessAdmin\DashboardController;
use App\Http\Controllers\BusinessAdmin\InventoryController;
use App\Http\Controllers\BusinessAdmin\InventoryHistoryController;
use App\Http\Controllers\BusinessAdmin\NotificationController;
use App\Http\Controllers\BusinessAdmin\PayrollController;
use App\Http\Controllers\BusinessAdmin\ProfileController;
use App\Http\Controllers\BusinessAdmin\ReportController;
use App\Http\Controllers\BusinessAdmin\RotaController;
use App\Http\Controllers\BusinessAdmin\SettingsController;
use App\Http\Controllers\BusinessAdmin\StaffController;
use App\Http\Controllers\BusinessAdmin\SubscriptionController;
use App\Http\Controllers\BusinessAdmin\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::post('/branch/select', [BranchController::class, 'select'])->name('branch.select');

Route::middleware('plan_feature:cash_up')->group(function (): void {
    Route::get('/cash-up', [CashUpController::class, 'index'])->name('cash-up');
    Route::post('/cash-up', [CashUpController::class, 'store'])->name('cash-up.store');
    Route::post('/cash-up/deductions', [CashUpController::class, 'storeDeductions'])->name('cash-up.deductions');
    Route::get('/cash-history', [CashHistoryController::class, 'index'])->name('cash-history');
});

Route::get('/staff', [StaffController::class, 'index'])->name('staff');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::post('/staff/{staff}/suspend', [StaffController::class, 'suspend'])->name('staff.suspend');
Route::post('/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.reset-password');

Route::middleware('plan_feature:attendance')->group(function (): void {
    Route::get('/clock-in', [ClockInController::class, 'index'])->name('clock-in');
    Route::post('/clock-in/verify', [ClockInController::class, 'verify'])->name('clock-in.verify');
    Route::post('/clock-in/action', [ClockInController::class, 'action'])->name('clock-in.action');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/entries', [AttendanceController::class, 'updateEntries'])->name('attendance.entries');
});

Route::middleware('plan_feature:inventory')->group(function (): void {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/categories', [InventoryController::class, 'storeCategory'])->name('inventory.categories.store');
    Route::post('/inventory/items', [InventoryController::class, 'storeItem'])->name('inventory.items.store');
    Route::post('/inventory/counts', [InventoryController::class, 'storeCount'])->name('inventory.counts.store');
    Route::get('/inventory-history', [InventoryHistoryController::class, 'index'])->name('inventory-history');
});

Route::middleware('plan_feature:suppliers')->group(function (): void {
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::post('/suppliers/invoices', [SupplierController::class, 'storeInvoice'])->name('suppliers.invoices.store');
    Route::post('/suppliers/invoices/{invoice}/paid', [SupplierController::class, 'markPaid'])->name('suppliers.invoices.paid');
});

Route::middleware('plan_feature:payroll')->group(function (): void {
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::post('/payroll/{wage}/paid', [PayrollController::class, 'markPaid'])->name('payroll.paid');
});

Route::middleware('plan_feature:rota')->group(function (): void {
    Route::get('/rota', [RotaController::class, 'index'])->name('rota');
    Route::post('/rota/sections', [RotaController::class, 'storeSection'])->name('rota.sections.store');
    Route::post('/rota/groups', [RotaController::class, 'storeGroup'])->name('rota.groups.store');
    Route::post('/rota/shifts', [RotaController::class, 'storeShift'])->name('rota.shifts.store');
    Route::delete('/rota/shifts/{shift}', [RotaController::class, 'destroyShift'])->name('rota.shifts.destroy');
});

Route::middleware('plan_feature:reports')->group(function (): void {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

Route::get('/branches', [BranchManageController::class, 'index'])->name('branches');
Route::post('/branches', [BranchManageController::class, 'store'])->name('branches.store');
Route::put('/branches/{branch}', [BranchManageController::class, 'update'])->name('branches.update');

Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
