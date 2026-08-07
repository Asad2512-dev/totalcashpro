<?php

declare(strict_types=1);

namespace App\Enums;

enum FinanceStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Approved => 'Approved',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
            self::Overdue => 'Overdue',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Approved => 'info',
            self::Paid => 'success',
            self::Cancelled => 'neutral',
            self::Overdue => 'danger',
        };
    }
}
