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
     * Authenticate Super Admin only for Phase 2.x.
     *
     * @throws ValidationException
     */
    public function attemptSuperAdmin(string $email, string $password, bool $remember = false): User
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

        if (! $user->isSuperAdmin()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Only Super Admin accounts can sign in during this phase.',
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $this->activityLogger->log(
            event: 'user.login',
            description: 'Super Admin signed in',
            actor: $user,
            subject: $user,
        );

        $this->auditLogger->log(
            action: 'auth.login',
            user: $user,
            target: $user,
        );

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
