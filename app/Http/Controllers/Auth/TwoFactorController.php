<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\LoginService;
use App\Services\Security\TwoFactorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly LoginService $loginService,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('login.id');

        if ($userId === null) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user === null) {
            $request->session()->forget(['login.id', 'login.remember', 'login.intended_route']);

            return redirect()->route('login');
        }

        if (! $this->twoFactorService->verifyLoginChallenge($user, $request->string('otp')->toString(), $request)) {
            return back()->withErrors(['otp' => 'The verification code is invalid or has expired.']);
        }

        $result = $this->loginService->completeTwoFactor($user, $request);

        $request->session()->forget(['login.id', 'login.remember', 'login.intended_route']);

        return redirect()->intended(route($result['route']));
    }

    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('login.id');

        if ($userId === null) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user !== null) {
            $this->twoFactorService->sendLoginChallenge($user, $request);
        }

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
