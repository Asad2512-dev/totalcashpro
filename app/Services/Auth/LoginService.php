<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\ServiceInterface;
use App\Enums\SecurityLogEvent;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use App\Services\Security\DeviceSessionService;
use App\Services\Security\LoginHistoryService;
use App\Services\Security\SecurityLogService;
use App\Services\Security\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginService implements ServiceInterface
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly AuditLogger $auditLogger,
        private readonly LoginHistoryService $loginHistoryService,
        private readonly SecurityLogService $securityLogService,
        private readonly DeviceSessionService $deviceSessionService,
        private readonly TwoFactorService $twoFactorService,
    ) {}

    /**
     * @return array{user: User, route: string, requires_two_factor: bool}
     *
     * @throws ValidationException
     */
    public function attempt(string $email, string $password, bool $remember = false, ?Request $request = null): array
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            if ($request !== null) {
                $this->loginHistoryService->recordFailure($email, $request, 'Invalid credentials');
                $this->securityLogService->log(
                    SecurityLogEvent::LoginFailure,
                    null,
                    'Failed login attempt for '.$email,
                    $request,
                );
            }

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('role');

        if (! $user->isActive()) {
            Auth::logout();

            if ($request !== null) {
                $this->loginHistoryService->recordFailure($email, $request, 'Inactive account');
            }

            throw ValidationException::withMessages([
                'email' => 'This account is inactive.',
            ]);
        }

        $route = $this->resolveRoute($user);

        if ($route === null) {
            Auth::logout();

            if ($request !== null) {
                $this->loginHistoryService->recordFailure($email, $request, 'Unauthorized role');
            }

            throw ValidationException::withMessages([
                'email' => 'This account cannot sign in.',
            ]);
        }

        if ($this->twoFactorService->requiresChallenge($user)) {
            Auth::logout();

            if ($request !== null) {
                $request->session()->put('login.id', $user->id);
                $request->session()->put('login.remember', $remember);
                $request->session()->put('login.intended_route', $route);
                $this->twoFactorService->sendLoginChallenge($user, $request);
            }

            return [
                'user' => $user,
                'route' => 'two-factor.challenge',
                'requires_two_factor' => true,
            ];
        }

        $this->completeLogin($user, $request);

        return [
            'user' => $user,
            'route' => $route,
            'requires_two_factor' => false,
        ];
    }

    public function completeTwoFactor(User $user, Request $request): array
    {
        $route = $this->resolveRoute($user);

        if ($route === null) {
            throw ValidationException::withMessages([
                'otp' => 'This account cannot sign in.',
            ]);
        }

        $remember = (bool) $request->session()->pull('login.remember', false);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $this->completeLogin($user, $request);

        return ['user' => $user, 'route' => $route];
    }

    /**
     * @deprecated Use attempt() — kept for any Super Admin-only callers.
     *
     * @throws ValidationException
     */
    public function attemptSuperAdmin(string $email, string $password, bool $remember = false): User
    {
        $result = $this->attempt($email, $password, $remember);

        if (! $result['user']->isSuperAdmin()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Only Super Admin accounts can sign in during this phase.',
            ]);
        }

        return $result['user'];
    }

    public function logout(?Request $request = null): void
    {
        $user = Auth::user();

        if ($user !== null && $request !== null) {
            $this->loginHistoryService->recordLogout($user, $request);
            $this->securityLogService->log(SecurityLogEvent::Logout, $user, 'User signed out', $request);

            UserDevice::query()
                ->where('user_id', $user->id)
                ->where('session_id', $request->session()->getId())
                ->whereNull('logged_out_at')
                ->update(['logged_out_at' => now(), 'is_current' => false]);
        }

        Auth::logout();
    }

    private function completeLogin(User $user, ?Request $request): void
    {
        $user->forceFill(['last_login_at' => now()])->save();

        $description = match (true) {
            $user->isSuperAdmin() => 'Super Admin signed in',
            $user->isAdmin() => 'Business Admin signed in',
            default => 'Staff signed in',
        };

        $action = match (true) {
            $user->isSuperAdmin() => 'auth.login',
            $user->isAdmin() => 'auth.login.business',
            default => 'auth.login.staff',
        };

        $this->activityLogger->log(
            event: 'user.login',
            description: $description,
            actor: $user,
            subject: $user,
        );

        $this->auditLogger->log(
            action: $action,
            user: $user,
            target: $user,
        );

        if ($request !== null) {
            $this->loginHistoryService->recordSuccess($user, $request);
            $this->securityLogService->log(SecurityLogEvent::LoginSuccess, $user, $description, $request);
            $this->deviceSessionService->register($user, $request);
        }
    }

    private function resolveRoute(User $user): ?string
    {
        if ($user->isSuperAdmin()) {
            return 'super-admin.dashboard';
        }

        if ($user->isAdmin() && $user->organization_id !== null) {
            return 'business-admin.dashboard';
        }

        if ($user->isStaff() && $user->organization_id !== null) {
            return 'staff.dashboard';
        }

        return null;
    }
}
