<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\SecurityLogEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

final class PasswordService
{
    public function __construct(
        private readonly SecurityLogService $securityLogService,
    ) {}

    /**
     * @return array<int, mixed>
     */
    public function rules(bool $confirmed = true): array
    {
        $rule = Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        if (! app()->environment('testing')) {
            $rule = $rule->uncompromised();
        }

        $rules = ['required', 'string', $rule];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    public function update(User $user, string $currentPassword, string $newPassword, Request $request): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->forceFill([
            'password' => $newPassword,
            'password_changed_at' => now(),
        ])->save();

        $this->securityLogService->log(
            SecurityLogEvent::PasswordChanged,
            $user,
            'Password changed successfully',
            $request,
        );
    }

    public function isExpired(User $user, int $days = 90): bool
    {
        if ($user->password_changed_at === null) {
            return false;
        }

        return $user->password_changed_at->addDays($days)->isPast();
    }
}
