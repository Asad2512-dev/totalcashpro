<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\PlanSelectionRequest;
use App\Services\Billing\PlanSelectionService;
use App\Services\Billing\TrialService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PlanSelectionController extends Controller
{
    public function __construct(
        private readonly PlanSelectionService $plans,
        private readonly TrialService $trials,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;

        if ($organization === null || ! $this->plans->requiresPlanSelection($organization)) {
            return redirect()->route('business-admin.dashboard');
        }

        return view('business-admin.subscription.choose-plan', [
            'plans' => $this->plans->selectablePlans(),
            'trial' => $this->trials->summary($organization),
            'organization' => $organization,
        ]);
    }

    public function store(PlanSelectionRequest $request): RedirectResponse
    {
        $user = $request->user();
        $organization = $user->organization;

        if ($organization === null) {
            return redirect()->route('business-admin.dashboard');
        }

        $this->plans->selectPlan($organization, $user, $request->validated('plan'));

        return redirect()
            ->route('business-admin.subscription')
            ->with('success', 'Plan selected. Billing integration coming soon — your selection has been saved.');
    }
}
