<?php

declare(strict_types=1);

namespace App\Enums;

enum RiderStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case OnLeave = 'on_leave';
    case Suspended = 'suspended';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::OnLeave => 'On leave',
            self::Suspended => 'Suspended',
            self::Archived => 'Archived',
        };
    }
}
