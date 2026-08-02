<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\ServiceInterface;
use App\Models\User;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginService implements ServiceInterface
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * Authenticate Super Admin or Business Admin and return the post-login route name.
     *
     * @throws ValidationException
     */
    public function attempt(string $email, string $password, bool $remember = false): array
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('role');

        if (! $user->isActive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'This account is inactive.',
            ]);
        }

        if ($user->isSuperAdmin()) {
            $user->forceFill(['last_login_at' => now()])->save();
            $this->logLogin($user, 'Super Admin signed in', 'auth.login');

            return ['user' => $user, 'route' => 'super-admin.dashboard'];
        }

        if ($user->isAdmin() && $user->organization_id !== null) {
            $user->forceFill(['last_login_at' => now()])->save();
            $this->logLogin($user, 'Business Admin signed in', 'auth.login.business');

            return ['user' => $user, 'route' => 'business-admin.dashboard'];
        }

        if ($user->isStaff() && $user->organization_id !== null) {
            $user->forceFill(['last_login_at' => now()])->save();
            $this->logLogin($user, 'Staff signed in', 'auth.login.staff');

            return ['user' => $user, 'route' => 'staff.dashboard'];
        }

        Auth::logout();

        throw ValidationException::withMessages([
            'email' => 'This account cannot sign in.',
        ]);
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

    public function logout(): void
    {
        Auth::logout();
    }

    private function logLogin(User $user, string $description, string $action): void
    {
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
    }
}
