<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final class OtpService
{
    public const EXPIRY_MINUTES = 10;

    public const CODE_LENGTH = 6;

    public function __construct(
        private readonly SecurityLogService $securityLogService,
    ) {}

    public function generateAndSend(User $user, OtpPurpose $purpose, ?Request $request = null): void
    {
        $key = $this->rateLimitKey($user, $purpose);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'otp' => 'Too many OTP requests. Please wait before trying again.',
            ]);
        }

        RateLimiter::hit($key, 60);

        OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose->value)
            ->whereNull('used_at')
            ->delete();

        $plainCode = $this->generatePlainCode();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'code_hash' => Hash::make($plainCode),
            'ip_address' => $request?->ip(),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        $user->notify(new OtpCodeNotification($plainCode, $purpose));

        $this->securityLogService->log(
            SecurityLogEvent::OtpRequested,
            $user,
            'OTP requested for '.$purpose->label(),
            $request,
            ['purpose' => $purpose->value],
        );
    }

    public function verify(User $user, OtpPurpose $purpose, string $code, ?Request $request = null): bool
    {
        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose->value)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if ($otp === null || $otp->isExpired()) {
            return false;
        }

        if (! Hash::check($code, $otp->code_hash)) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        $this->securityLogService->log(
            SecurityLogEvent::OtpVerified,
            $user,
            'OTP verified for '.$purpose->label(),
            $request,
            ['purpose' => $purpose->value],
        );

        return true;
    }

    public function verifyOrFail(User $user, OtpPurpose $purpose, string $code, ?Request $request = null): void
    {
        if (! $this->verify($user, $purpose, $code, $request)) {
            throw ValidationException::withMessages([
                'otp' => 'The verification code is invalid or has expired.',
            ]);
        }
    }

    private function generatePlainCode(): string
    {
        return str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    private function rateLimitKey(User $user, OtpPurpose $purpose): string
    {
        return 'otp:'.$user->id.':'.$purpose->value;
    }
}
