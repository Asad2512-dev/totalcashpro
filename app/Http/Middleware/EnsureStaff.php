<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanFeature;
use App\Services\Billing\FeatureAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureStaff
{
    public function __construct(private readonly FeatureAccessService $features) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->isActive()) {
            abort(403, 'Your account is not active.');
        }

        if (! $user->isStaff()) {
            abort(403, 'You must be a staff member to access this area.');
        }

        if ($user->organization_id === null) {
            abort(403, 'You must belong to an organization to access this area.');
        }

        $user->loadMissing('organization');

        if (! $this->features->organizationIsAccessible($user->organization)) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This business account is not active. Contact your manager.']);
        }

        if (! $this->features->can($user, PlanFeature::StaffPanel)) {
            abort(403, 'Staff Panel is not included in your organisation plan.');
        }

        return $next($request);
    }
}
