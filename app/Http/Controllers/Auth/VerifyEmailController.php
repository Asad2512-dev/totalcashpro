<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class VerifyEmailController extends Controller
{
    public function notice(Request $request)
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended($this->dashboardRoute($request->user()));
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        $user = $request->user();

        return redirect()
            ->intended($this->postVerifyRoute($user))
            ->with('status', 'Your email has been verified successfully.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended($this->dashboardRoute($request->user()));
        }

        $request->user()?->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    private function dashboardRoute(?\App\Models\User $user): string
    {
        if ($user === null) {
            return route('login');
        }

        if ($user->isSuperAdmin()) {
            return route('super-admin.dashboard');
        }

        if ($user->isStaff()) {
            return route('staff.dashboard');
        }

        return route('business-admin.dashboard');
    }

    private function postVerifyRoute(?\App\Models\User $user): string
    {
        if ($user === null) {
            return route('login');
        }

        if ($user->isAdmin() && ! $user->hasCompletedOnboarding()) {
            return route('business-admin.onboarding');
        }

        return $this->dashboardRoute($user);
    }
}
