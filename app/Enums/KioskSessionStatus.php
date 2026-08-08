<?php

declare(strict_types=1);

namespace App\Enums;

enum KioskSessionStatus: string
{
    case Active = 'active';
    case LoggedOut = 'logged_out';
    case Revoked = 'revoked';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::LoggedOut => 'Logged out',
            self::Revoked => 'Revoked',
            self::Expired => 'Expired',
        };
    }
}
