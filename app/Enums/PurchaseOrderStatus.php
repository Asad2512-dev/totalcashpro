<?php

declare(strict_types=1);

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Ordered = 'ordered';
    case PartiallyReceived = 'partial';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Ordered => 'Ordered',
            self::PartiallyReceived => 'Partially received',
            self::Received => 'Received',
            self::Cancelled => 'Cancelled',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Ordered => 'info',
            self::PartiallyReceived => 'warning',
            self::Received => 'success',
            self::Cancelled => 'danger',
        };
    }
}
