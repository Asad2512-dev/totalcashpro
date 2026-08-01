<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Bind repository contracts to their Eloquent implementations.
 *
 * Register new repository bindings here as SaaS modules are introduced.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository interface => implementation map.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // Example for Phase 2+:
        // \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\UserRepository::class,
    ];

    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
