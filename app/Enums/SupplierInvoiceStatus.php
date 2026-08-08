<?php

declare(strict_types=1);

namespace App\Enums;

enum SupplierInvoiceStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Outstanding = 'outstanding';
    case Disputed = 'disputed';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::PartiallyPaid => 'Partially paid',
            self::Paid => 'Paid',
            self::Outstanding => 'Outstanding',
            self::Disputed => 'Disputed',
            self::Overdue => 'Overdue',
            self::Cancelled => 'Cancelled',
        };
    }

    public static function tryFromLegacy(string $value): self
    {
        return match ($value) {
            'Pending' => self::Pending,
            'Paid' => self::Paid,
            default => self::tryFrom($value) ?? self::Pending,
        };
    }
}
