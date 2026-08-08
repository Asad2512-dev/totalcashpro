<?php

declare(strict_types=1);

namespace App\Enums;

enum CashDrawerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Locked = 'locked';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Locked => 'Locked',
            self::Archived => 'Archived',
        };
    }

    public function isUsableForCashUp(): bool
    {
        return $this === self::Active;
    }
}
