<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ContactMessageSubmitted;
use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Events\OrganizationRegistered;
use App\Events\PurchaseOrderReceived;
use App\Events\ShiftSwapApproved;
use App\Events\ShiftSwapRejected;
use App\Events\OwnerCredentialsSent;
use App\Events\RecurringBillGenerated;
use App\Events\StaffInvited;
use App\Events\StaffPasswordReset;
use App\Listeners\CreateFinanceRecordsFromPurchaseOrder;
use App\Listeners\LogOrganizationRegistered;
use App\Listeners\NotifyLeaveApproved;
use App\Listeners\NotifyLeaveRejected;
use App\Listeners\NotifyRecurringBillGenerated;
use App\Listeners\NotifyShiftSwapRejected;
use App\Listeners\ProcessShiftSwapApproval;
use App\Listeners\SendContactMessageNotification;
use App\Listeners\SendOwnerCredentialsEmail;
use App\Listeners\SendStaffInvitationEmail;
use App\Listeners\SendStaffPasswordResetEmail;
use App\Listeners\SendWelcomeEmail;
use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Models\FinanceSupplierPayment;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Spending;
use App\Models\SupplierInvoice;
use App\Models\Wage;
use App\Observers\CashUpFinanceSyncObserver;
use App\Observers\ReportCenterCacheObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Event::listen(OrganizationRegistered::class, LogOrganizationRegistered::class);
        Event::listen(OrganizationRegistered::class, SendWelcomeEmail::class);
        Event::listen(PurchaseOrderReceived::class, CreateFinanceRecordsFromPurchaseOrder::class);
        Event::listen(LeaveRequestApproved::class, NotifyLeaveApproved::class);
        Event::listen(LeaveRequestRejected::class, NotifyLeaveRejected::class);
        Event::listen(ShiftSwapApproved::class, ProcessShiftSwapApproval::class);
        Event::listen(ShiftSwapRejected::class, NotifyShiftSwapRejected::class);
        Event::listen(StaffInvited::class, SendStaffInvitationEmail::class);
        Event::listen(StaffPasswordReset::class, SendStaffPasswordResetEmail::class);
        Event::listen(OwnerCredentialsSent::class, SendOwnerCredentialsEmail::class);
        Event::listen(ContactMessageSubmitted::class, SendContactMessageNotification::class);
        Event::listen(RecurringBillGenerated::class, NotifyRecurringBillGenerated::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Keep generated asset/route URLs on the host the browser is actually using in local.
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            $request = $this->app['request'] ?? null;
            if ($request !== null && filled($request->getSchemeAndHttpHost())) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }

        $reportObserver = ReportCenterCacheObserver::class;
        CashUp::observe(CashUpFinanceSyncObserver::class);
        CashUp::observe($reportObserver);
        Bill::observe($reportObserver);
        Spending::observe($reportObserver);
        Wage::observe($reportObserver);
        FinanceIncomeEntry::observe($reportObserver);
        FinanceSupplierPayment::observe($reportObserver);
        SupplierInvoice::observe($reportObserver);
        PurchaseOrder::observe($reportObserver);
        InventoryCount::observe($reportObserver);
        InventoryItem::observe($reportObserver);
        AttendanceLog::observe($reportObserver);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('otp', fn () => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(request()->ip()));

        RateLimiter::for('password-reset', fn () => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(request()->ip()));

        RateLimiter::for('api-tokens', fn () => \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by(request()->user()?->id ?: request()->ip()));
    }
}
