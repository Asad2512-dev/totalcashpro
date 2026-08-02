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
    ];

    public function register(): void
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
