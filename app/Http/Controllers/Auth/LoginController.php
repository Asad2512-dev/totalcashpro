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
        if (Auth::check() && Auth::user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
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

        $this->loginService->attemptSuperAdmin(
            $credentials['email'],
            $credentials['password'],
            (bool) ($credentials['remember'] ?? false),
        );

        $request->session()->regenerate();

        return redirect()->intended(route('super-admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->loginService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function forgot(): View
    {
        return view('auth.forgot-password');
    }

    public function sendReset(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Password reset will be available in a later phase. Contact support if needed.');
    }
}
