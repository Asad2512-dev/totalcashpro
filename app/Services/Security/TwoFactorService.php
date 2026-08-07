<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Enums\TwoFactorMethod;
use App\Models\TwoFactorRecoveryCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class TwoFactorService
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly SecurityLogService $securityLogService,
    ) {}

    public function isEnabled(User $user): bool
    {
        return (bool) $user->two_factor_enabled;
    }

    public function requiresChallenge(User $user): bool
    {
        return $this->isEnabled($user) && $user->two_factor_confirmed_at !== null;
    }

    public function enableWithEmail(User $user, string $otp, Request $request): Collection
    {
        $this->otpService->verifyOrFail($user, OtpPurpose::TwoFactorSetup, $otp, $request);

        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_method' => TwoFactorMethod::Email->value,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $codes = $this->regenerateRecoveryCodes($user);

        $this->securityLogService->log(
            SecurityLogEvent::TwoFactorEnabled,
            $user,
            'Two-factor authentication enabled (email OTP)',
            $request,
        );

        return $codes;
    }

    public function disable(User $user, Request $request): void
    {
        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_method' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        TwoFactorRecoveryCode::query()->where('user_id', $user->id)->delete();

        $this->securityLogService->log(
            SecurityLogEvent::TwoFactorDisabled,
            $user,
            'Two-factor authentication disabled',
            $request,
        );
    }

    public function sendLoginChallenge(User $user, Request $request): void
    {
        $this->otpService->generateAndSend($user, OtpPurpose::TwoFactorLogin, $request);
    }

    public function verifyLoginChallenge(User $user, string $code, Request $request): bool
    {
        if ($this->otpService->verify($user, OtpPurpose::TwoFactorLogin, $code, $request)) {
            return true;
        }

        return $this->useRecoveryCode($user, $code, $request);
    }

    public function useRecoveryCode(User $user, string $code, Request $request): bool
    {
        $recoveryCodes = TwoFactorRecoveryCode::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->get();

        foreach ($recoveryCodes as $stored) {
            if (Hash::check(strtoupper(trim($code)), $stored->code_hash)) {
                $stored->update(['used_at' => now()]);

                $this->securityLogService->log(
                    SecurityLogEvent::RecoveryCodeUsed,
                    $user,
                    'Recovery code used for login',
                    $request,
                );

                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, string>
     */
    public function regenerateRecoveryCodes(User $user): Collection
    {
        TwoFactorRecoveryCode::query()->where('user_id', $user->id)->delete();

        $plainCodes = collect();

        for ($i = 0; $i < 8; $i++) {
            $plain = strtoupper(Str::random(4).'-'.Str::random(4));
            $plainCodes->push($plain);

            TwoFactorRecoveryCode::query()->create([
                'user_id' => $user->id,
                'code_hash' => Hash::make($plain),
            ]);
        }

        return $plainCodes;
    }
}
