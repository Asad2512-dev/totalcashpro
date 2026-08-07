<?php

declare(strict_types=1);

namespace App\Enums;

enum BillStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Approved => 'Approved',
            self::Pending => 'Approved',
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
            self::Cancelled => 'Cancelled',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Approved, self::Pending => 'info',
            self::Paid => 'success',
            self::Overdue => 'danger',
            self::Cancelled => 'neutral',
        };
    }
}
