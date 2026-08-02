<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'business_admin' => \App\Http\Middleware\EnsureBusinessAdmin::class,
            'staff' => \App\Http\Middleware\EnsureStaff::class,
            'org_active' => \App\Http\Middleware\EnsureOrganizationActive::class,
            'plan_feature' => \App\Http\Middleware\EnsurePlanFeature::class,
            'onboarding_complete' => \App\Http\Middleware\EnsureOnboardingComplete::class,
            'plan_selected' => \App\Http\Middleware\EnsurePlanSelected::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            if ($user?->isSuperAdmin()) {
                return route('super-admin.dashboard');
            }

            if ($user?->isAdmin() && $user->organization_id) {
                return route('business-admin.dashboard');
            }

            if ($user?->isStaff() && $user->organization_id) {
                return route('staff.dashboard');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
