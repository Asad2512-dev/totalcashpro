<?php

declare(strict_types=1);

namespace App\Enums;

enum TwoFactorMethod: string
{
    case Email = 'email';
    case Totp = 'totp';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Email OTP',
            self::Totp => 'Authenticator app',
        };
    }
}
