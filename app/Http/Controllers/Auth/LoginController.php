<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {}

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user?->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            }
            if ($user?->isAdmin() && $user->organization_id) {
                return redirect()->route('business-admin.dashboard');
            }
            if ($user?->isStaff() && $user->organization_id) {
                return redirect()->route('staff.dashboard');
            }
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $result = $this->loginService->attempt(
            $credentials['email'],
            $credentials['password'],
            (bool) ($credentials['remember'] ?? false),
            $request,
        );

        if ($result['requires_two_factor']) {
            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($result['route']));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->loginService->logout($request);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
