<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanFeature;
use App\Services\Billing\FeatureAccessService;
use App\Services\BusinessAdmin\RiderService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureRider
{
    public function __construct(
        private readonly FeatureAccessService $features,
        private readonly RiderService $riders,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->isActive()) {
            abort(403, 'Your account is not active.');
        }

        if (! $user->isRider()) {
            abort(403, 'You must be a rider to access this area.');
        }

        if ($user->organization_id === null) {
            abort(403, 'You must belong to an organization.');
        }

        $user->loadMissing('organization');

        if (! $this->features->organizationIsAccessible($user->organization)) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'This business account is not active.']);
        }

        if (! $this->features->can($user, PlanFeature::Inventory)) {
            abort(403, 'Inventory is not included in your organisation plan.');
        }

        $rider = $this->riders->findForUser($user);
        if ($rider === null || ! $rider->is_active) {
            abort(403, 'Your rider profile is not active.');
        }

        $request->attributes->set('rider', $rider);

        return $next($request);
    }
}
