<?php

declare(strict_types=1);

use App\Http\Controllers\BusinessAdmin\AccountingController;
use App\Http\Controllers\BusinessAdmin\AttendanceController;
use App\Http\Controllers\BusinessAdmin\BranchController;
use App\Http\Controllers\BusinessAdmin\BranchManageController;
use App\Http\Controllers\BusinessAdmin\CashHistoryController;
use App\Http\Controllers\BusinessAdmin\CashUpController;
use App\Http\Controllers\BusinessAdmin\CrmController;
use App\Http\Controllers\BusinessAdmin\DashboardController;
use App\Http\Controllers\BusinessAdmin\FinanceController;
use App\Http\Controllers\BusinessAdmin\FinanceToolsController;
use App\Http\Controllers\BusinessAdmin\HrController;
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
Route::get('/hr', [HrController::class, 'index'])->name('hr');
Route::post('/hr/leave/{leaveRequest}/review', [HrController::class, 'reviewLeave'])->name('hr.leave.review');
Route::post('/hr/shift-swaps/{shiftSwapRequest}/review', [HrController::class, 'reviewShiftSwap'])->name('hr.shift-swap.review');
Route::get('/crm', [CrmController::class, 'index'])->name('crm');
Route::post('/crm', [CrmController::class, 'store'])->name('crm.store');
Route::get('/crm/{crmCustomer}', [CrmController::class, 'show'])->name('crm.show');
Route::put('/crm/{crmCustomer}', [CrmController::class, 'update'])->name('crm.update');
Route::delete('/crm/{crmCustomer}', [CrmController::class, 'destroy'])->name('crm.destroy');
Route::post('/crm/{crmCustomer}/notes', [CrmController::class, 'storeNote'])->name('crm.notes.store');
Route::post('/crm/{crmCustomer}/visits', [CrmController::class, 'storeVisit'])->name('crm.visits.store');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::post('/staff/{staff}/suspend', [StaffController::class, 'suspend'])->name('staff.suspend');
Route::post('/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.reset-password');
Route::post('/staff/{staff}/reset-pin', [StaffController::class, 'resetPin'])->name('staff.reset-pin');

Route::middleware('plan_feature:attendance')->group(function (): void {
    Route::redirect('/clock-in', '/business-admin/kiosks')->name('clock-in');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/entries', [AttendanceController::class, 'updateEntries'])->name('attendance.entries');

    Route::controller(\App\Http\Controllers\BusinessAdmin\BranchKioskController::class)->prefix('kiosks')->name('kiosks.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{kiosk}', 'update')->name('update');
        Route::post('/{kiosk}/regenerate-token', 'regenerateToken')->name('regenerate-token');
        Route::post('/{kiosk}/disable', 'disable')->name('disable');
        Route::post('/{kiosk}/enable', 'enable')->name('enable');
        Route::post('/{kiosk}/reset', 'reset')->name('reset');
        Route::post('/{kiosk}/force-logout', 'forceLogout')->name('force-logout');
        Route::get('/{kiosk}/activity', 'activity')->name('activity');
    });
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

    Route::controller(\App\Http\Controllers\BusinessAdmin\PurchaseOrderController::class)->prefix('purchase-orders')->name('purchase-orders.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{purchaseOrder}', 'show')->name('show');
        Route::put('/{purchaseOrder}', 'update')->name('update');
        Route::post('/{purchaseOrder}/submit', 'submit')->name('submit');
        Route::post('/{purchaseOrder}/approve', 'approve')->name('approve');
        Route::post('/{purchaseOrder}/order', 'order')->name('order');
        Route::post('/{purchaseOrder}/cancel', 'cancel')->name('cancel');
        Route::post('/{purchaseOrder}/receive', 'receive')->name('receive');
    });
});

Route::middleware('plan_feature:accounting')->group(function (): void {
    Route::get('/accounting', fn () => redirect()->route('business-admin.finance.dashboard'))->name('accounting');

    Route::controller(FinanceController::class)->prefix('finance')->name('finance.')->group(function (): void {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('/income', 'income')->name('income');
        Route::post('/income', 'storeIncome')->name('income.store');
        Route::post('/income/{entry}/approve', 'approveIncome')->name('income.approve');
        Route::post('/income/{entry}/paid', 'markIncomePaid')->name('income.paid');

        Route::get('/expenses', 'expenses')->name('expenses');
        Route::post('/expenses', 'storeExpense')->name('expenses.store');
        Route::post('/expenses/{expense}/approve', 'approveExpense')->name('expenses.approve');
        Route::post('/expenses/{expense}/paid', 'markExpensePaid')->name('expenses.paid');

        Route::get('/bills', 'bills')->name('bills');
        Route::post('/bills', 'storeBill')->name('bills.store');
        Route::post('/bills/{bill}/approve', 'approveBill')->name('bills.approve');
        Route::post('/bills/{bill}/paid', 'markBillPaid')->name('bills.paid');

        Route::get('/purchase-invoices', 'purchaseInvoices')->name('purchase-invoices');
        Route::post('/purchase-invoices', 'storePurchaseInvoice')->name('purchase-invoices.store');

        Route::get('/supplier-payments', 'supplierPayments')->name('supplier-payments');
        Route::post('/supplier-payments', 'storeSupplierPayment')->name('supplier-payments.store');

        Route::get('/bank-accounts', 'bankAccounts')->name('bank-accounts');
        Route::post('/bank-accounts', 'storeBankAccount')->name('bank-accounts.store');

        Route::get('/cash-flow', 'cashFlow')->name('cash-flow');
        Route::get('/profit-loss', 'profitLoss')->name('profit-loss');
        Route::get('/vat', 'vatSummary')->name('vat');
        Route::get('/reports', 'reports')->name('reports');
        Route::get('/reports/export', 'exportReports')->name('reports.export');
    });

    Route::controller(FinanceToolsController::class)->prefix('finance')->name('finance.')->group(function (): void {
        Route::get('/recurring-bills', 'recurringBills')->name('recurring-bills');
        Route::post('/recurring-bills', 'storeRecurringBill')->name('recurring-bills.store');
        Route::get('/petty-cash', 'pettyCash')->name('petty-cash');
        Route::post('/petty-cash', 'storePettyCashAccount')->name('petty-cash.store');
        Route::post('/petty-cash/{account}/transactions', 'storePettyCashTransaction')->name('petty-cash.transactions.store');
        Route::get('/cash-drawers', 'cashDrawers')->name('cash-drawers');
        Route::put('/cash-drawers/{drawer}', 'updateCashDrawer')->name('cash-drawers.update');
    });
});

Route::middleware('plan_feature:payroll')->group(function (): void {
    Route::get('/payroll', fn () => redirect()->route('business-admin.finance.payroll'))->name('payroll');

    Route::controller(FinanceController::class)->prefix('finance')->name('finance.')->group(function (): void {
        Route::get('/payroll', 'payroll')->name('payroll');
        Route::post('/payroll', 'storePayroll')->name('payroll.store');
        Route::post('/payroll/generate', 'generatePayroll')->name('payroll.generate');
        Route::post('/payroll/runs/{run}/approve', 'approvePayrollRun')->name('payroll.approve-run');
        Route::post('/payroll/{wage}/approve', 'approveWage')->name('payroll.approve-wage');
        Route::post('/payroll/{wage}/paid', 'markWagePaid')->name('payroll.paid');
        Route::get('/weekly-wages', 'weeklyWages')->name('weekly-wages');
    });
});

Route::middleware('plan_feature:accounting')->group(function (): void {
    Route::post('/accounting/bills', [AccountingController::class, 'storeBill'])->name('accounting.bills.store');
    Route::post('/accounting/bills/{bill}/paid', [AccountingController::class, 'markBillPaid'])->name('accounting.bills.paid');
    Route::post('/accounting/spendings', [AccountingController::class, 'storeSpending'])->name('accounting.spendings.store');
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
    Route::post('/reports/saved', [ReportController::class, 'storeSaved'])->name('reports.saved');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
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

Route::prefix('security')->name('security.')->group(base_path('routes/security.php'));
