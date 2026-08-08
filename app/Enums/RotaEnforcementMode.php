<?php

declare(strict_types=1);

namespace App\Enums;

enum RotaEnforcementMode: string
{
    case Disabled = 'disabled';
    case Soft = 'soft';
    case Strict = 'strict';

    public function label(): string
    {
        return match ($this) {
            self::Disabled => 'Disabled',
            self::Soft => 'Soft warning',
            self::Strict => 'Strict',
        };
    }
}
