<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\OnboardingRequest;
use App\Services\Billing\TrialService;
use App\Services\Onboarding\OnboardingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class OnboardingController extends Controller
{
    public function __construct(
        private readonly OnboardingService $onboarding,
        private readonly TrialService $trials,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $organization = $user->organization;
        $branch = $organization?->branches()->orderBy('id')->first();

        if (! $this->onboarding->needsOnboarding($user)) {
            return redirect()->route('business-admin.dashboard');
        }

        $step = max(1, min(OnboardingService::TOTAL_STEPS, (int) $request->query('step', 1)));

        return view('business-admin.onboarding.index', [
            'step' => $step,
            'totalSteps' => OnboardingService::TOTAL_STEPS,
            'organization' => $organization,
            'branch' => $branch,
            'drawer' => $branch?->cashDrawer,
            'trial' => $organization ? $this->trials->summary($organization) : null,
            'currencies' => ['GBP', 'EUR', 'USD'],
            'timezones' => ['Europe/London', 'Europe/Dublin', 'Europe/Paris'],
        ]);
    }

    public function store(OnboardingRequest $request): RedirectResponse
    {
        $user = $request->user();
        $organization = $user->organization;
        $branch = $organization?->branches()->orderBy('id')->first();
        $step = (int) $request->input('step', 1);

        if ($step === 2 && $organization !== null) {
            $this->onboarding->updateBusiness($organization, $request->businessPayload());
        }

        if ($step === 3 && $branch !== null) {
            $this->onboarding->updateBranch($branch, $request->branchPayload());
        }

        if ($step === 4 && $organization !== null) {
            $this->onboarding->updateSettings($organization, $request->settingsPayload());
        }

        if ($step === 5 && $organization !== null && $branch !== null) {
            $this->onboarding->ensureCashDrawer(
                $organization,
                $branch,
                (float) ($request->input('drawer_opening_balance') ?? 0),
            );
        }

        if ($step < OnboardingService::TOTAL_STEPS) {
            return redirect()->route('business-admin.onboarding', ['step' => $step + 1]);
        }

        $this->onboarding->finalizeSetup(
            $user,
            $request->businessPayload(),
            $request->branchPayload(),
            $request->settingsPayload(),
            (float) ($request->input('drawer_opening_balance') ?? 0),
            $request->staffInviteEmails(),
            $request->supplierPayload(),
        );

        return redirect()
            ->route('business-admin.dashboard')
            ->with('success', 'Setup complete. Welcome to your dashboard.');
    }

    public function skip(Request $request): RedirectResponse
    {
        $this->onboarding->complete($request->user());

        return redirect()
            ->route('business-admin.dashboard')
            ->with('success', 'You can finish setup anytime from Settings.');
    }
}
