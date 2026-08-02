<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanFeature;
use App\Services\Billing\FeatureAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePlanFeature
{
    public function __construct(private readonly FeatureAccessService $features) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $enum = PlanFeature::tryFrom($feature);
        if ($enum === null) {
            abort(500, 'Unknown plan feature: '.$feature);
        }

        if (! $this->features->can($user, $enum)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Your current plan does not include {$enum->label()}.",
                ], 403);
            }

            return redirect()
                ->route($user->isStaff() ? 'staff.dashboard' : 'business-admin.subscription')
                ->with('status', "Upgrade required: {$enum->label()} is not on your plan.");
        }

        return $next($request);
    }
}
