<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Billing\PlanSelectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePlanSelected
{
    public function __construct(
        private readonly PlanSelectionService $plans,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $organization = $user?->organization;

        if ($user === null || ! $user->isAdmin() || $organization === null) {
            return $next($request);
        }

        if ($this->plans->requiresPlanSelection($organization)) {
            if (! $request->routeIs('business-admin.subscription.choose-plan', 'business-admin.subscription.choose-plan.store', 'logout')) {
                return redirect()
                    ->route('business-admin.subscription.choose-plan')
                    ->with('warning', 'Your trial has ended. Please choose a plan to continue.');
            }
        }

        return $next($request);
    }
}
