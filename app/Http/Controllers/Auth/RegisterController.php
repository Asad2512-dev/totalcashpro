<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTOs\SignupData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Onboarding\SignupService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly SignupService $signupService,
    ) {}

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('business-admin.dashboard'));
        }

        return view('auth.register', [
            'businessTypes' => \App\Enums\BusinessType::options(),
            'countries' => $this->countries(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $result = $this->signupService->register(
            SignupData::fromValidated($request->validated()),
        );

        $owner = $result->owner;

        event(new Registered($owner));

        Auth::login($owner);

        $request->session()->regenerate();

        return redirect()
            ->route('verification.notice')
            ->with('status', 'Welcome! Your 14-day Professional trial has started. Please verify your email to continue.');
    }

    /**
     * @return list<array{code: string, label: string}>
     */
    private function countries(): array
    {
        return [
            ['code' => 'GB', 'label' => 'United Kingdom'],
            ['code' => 'IE', 'label' => 'Ireland'],
            ['code' => 'US', 'label' => 'United States'],
            ['code' => 'CA', 'label' => 'Canada'],
            ['code' => 'AU', 'label' => 'Australia'],
            ['code' => 'NZ', 'label' => 'New Zealand'],
            ['code' => 'DE', 'label' => 'Germany'],
            ['code' => 'FR', 'label' => 'France'],
            ['code' => 'ES', 'label' => 'Spain'],
            ['code' => 'IT', 'label' => 'Italy'],
            ['code' => 'NL', 'label' => 'Netherlands'],
            ['code' => 'AE', 'label' => 'United Arab Emirates'],
            ['code' => 'IN', 'label' => 'India'],
            ['code' => 'PK', 'label' => 'Pakistan'],
        ];
    }
}
