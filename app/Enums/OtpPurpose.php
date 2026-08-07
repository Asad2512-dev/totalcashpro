<?php

declare(strict_types=1);

namespace App\Enums;

enum OtpPurpose: string
{
    case EmailVerification = 'email_verification';
    case TwoFactorLogin = 'two_factor_login';
    case TwoFactorSetup = 'two_factor_setup';
    case SensitiveAction = 'sensitive_action';
    case PasswordReset = 'password_reset';

    public function label(): string
    {
        return match ($this) {
            self::EmailVerification => 'Email verification',
            self::TwoFactorLogin => 'Two-factor login',
            self::TwoFactorSetup => 'Two-factor setup',
            self::SensitiveAction => 'Sensitive action',
            self::PasswordReset => 'Password reset',
        };
    }
}
