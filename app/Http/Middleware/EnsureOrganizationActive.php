<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Billing\FeatureAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureOrganizationActive
{
    public function __construct(private readonly FeatureAccessService $features) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->isSuperAdmin()) {
            return $next($request);
        }

        $user->loadMissing('organization');

        if (! $this->features->organizationIsAccessible($user->organization)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This business account is suspended or inactive.']);
        }

        // Only enforce subscription state when one exists. Orgs on trial/active
        // without a subscription row still get Basic entitlements via FeatureAccessService.
        $subscription = $user->organization?->currentSubscription;
        if ($subscription !== null && ! $this->features->subscriptionIsUsable($subscription)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your subscription is not active. Contact support.']);
        }

        return $next($request);
    }
}
