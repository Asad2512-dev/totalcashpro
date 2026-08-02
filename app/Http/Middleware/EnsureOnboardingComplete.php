<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Onboarding\OnboardingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureOnboardingComplete
{
    public function __construct(
        private readonly OnboardingService $onboarding,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isAdmin()) {
            return $next($request);
        }

        if ($this->onboarding->needsOnboarding($user)) {
            return redirect()->route('business-admin.onboarding');
        }

        return $next($request);
    }
}
