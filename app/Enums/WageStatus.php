<?php

declare(strict_types=1);

namespace App\Enums;

enum WageStatus: string
{
    case Draft = 'Draft';
    case Approved = 'Approved';
    case Pending = 'Pending';
    case Paid = 'Paid';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Approved, self::Pending => 'Approved',
            self::Paid => 'Paid',
        };
    }

    public function isPayable(): bool
    {
        return in_array($this, [self::Approved, self::Pending], true);
    }
}
