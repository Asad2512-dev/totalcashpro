<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class ProfileEmailService
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly SecurityLogService $securityLogService,
    ) {}

    public function requestChange(User $user, string $newEmail, Request $request): void
    {
        $newEmail = strtolower(trim($newEmail));

        if ($newEmail === $user->email) {
            throw ValidationException::withMessages([
                'email' => 'That is already your email address.',
            ]);
        }

        if (User::query()->where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email is already in use.',
            ]);
        }

        $request->session()->put('pending_email_change', $newEmail);
        $this->otpService->generateAndSend($user, OtpPurpose::SensitiveAction, $request);
    }

    public function confirmChange(User $user, string $otp, Request $request): void
    {
        $newEmail = $request->session()->pull('pending_email_change');

        if ($newEmail === null) {
            throw ValidationException::withMessages([
                'otp' => 'No pending email change found. Please start again.',
            ]);
        }

        $this->otpService->verifyOrFail($user, OtpPurpose::SensitiveAction, $otp, $request);

        $oldEmail = $user->email;

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();

        $this->securityLogService->log(
            SecurityLogEvent::EmailChanged,
            $user,
            "Email changed from {$oldEmail} to {$newEmail}",
            $request,
            ['old_email' => $oldEmail, 'new_email' => $newEmail],
        );
    }
}
