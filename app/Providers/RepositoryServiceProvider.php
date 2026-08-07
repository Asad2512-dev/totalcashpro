<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Bind repository contracts to their Eloquent implementations.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        \App\Repositories\Contracts\RoleRepositoryInterface::class => \App\Repositories\Eloquent\RoleRepository::class,
        \App\Repositories\Contracts\OrganizationRepositoryInterface::class => \App\Repositories\Eloquent\OrganizationRepository::class,
        \App\Repositories\Contracts\PlanRepositoryInterface::class => \App\Repositories\Eloquent\PlanRepository::class,
        \App\Repositories\Contracts\SubscriptionRepositoryInterface::class => \App\Repositories\Eloquent\SubscriptionRepository::class,
        \App\Repositories\Contracts\PaymentRepositoryInterface::class => \App\Repositories\Eloquent\PaymentRepository::class,
        \App\Repositories\Contracts\CashUpRepositoryInterface::class => \App\Repositories\Eloquent\CashUpRepository::class,
        \App\Repositories\Contracts\StaffRepositoryInterface::class => \App\Repositories\Eloquent\StaffRepository::class,
        \App\Repositories\Contracts\AttendanceRepositoryInterface::class => \App\Repositories\Eloquent\AttendanceRepository::class,
        \App\Repositories\Contracts\InventoryRepositoryInterface::class => \App\Repositories\Eloquent\InventoryRepository::class,
        \App\Repositories\Contracts\SupplierRepositoryInterface::class => \App\Repositories\Eloquent\SupplierRepository::class,
        \App\Repositories\Contracts\WageRepositoryInterface::class => \App\Repositories\Eloquent\WageRepository::class,
        \App\Repositories\Contracts\RotaRepositoryInterface::class => \App\Repositories\Eloquent\RotaRepository::class,
        \App\Repositories\Contracts\BillRepositoryInterface::class => \App\Repositories\Eloquent\BillRepository::class,
        \App\Repositories\Contracts\SpendingRepositoryInterface::class => \App\Repositories\Eloquent\SpendingRepository::class,
        \App\Repositories\Contracts\FinanceBankAccountRepositoryInterface::class => \App\Repositories\Eloquent\FinanceBankAccountRepository::class,
        \App\Repositories\Contracts\FinanceIncomeRepositoryInterface::class => \App\Repositories\Eloquent\FinanceIncomeRepository::class,
        \App\Repositories\Contracts\FinancePayrollRunRepositoryInterface::class => \App\Repositories\Eloquent\FinancePayrollRunRepository::class,
        \App\Repositories\Contracts\FinanceSupplierPaymentRepositoryInterface::class => \App\Repositories\Eloquent\FinanceSupplierPaymentRepository::class,
        \App\Repositories\Contracts\FinanceAttachmentRepositoryInterface::class => \App\Repositories\Eloquent\FinanceAttachmentRepository::class,
        \App\Repositories\Contracts\PurchaseOrderRepositoryInterface::class => \App\Repositories\Eloquent\PurchaseOrderRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
