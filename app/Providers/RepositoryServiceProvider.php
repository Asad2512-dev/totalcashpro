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
    ];

    public function register(): void
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
