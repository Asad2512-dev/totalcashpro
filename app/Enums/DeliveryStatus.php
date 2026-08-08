<?php

declare(strict_types=1);

namespace App\Enums;

enum DeliveryStatus: string
{
    case Assigned = 'assigned';
    case Accepted = 'accepted';
    case AtSupplier = 'at_supplier';
    case Collected = 'collected';
    case OutForDelivery = 'out_for_delivery';
    case Arrived = 'arrived';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Assigned',
            self::Accepted => 'Accepted',
            self::AtSupplier => 'At supplier',
            self::Collected => 'Collected',
            self::OutForDelivery => 'Out for delivery',
            self::Arrived => 'Arrived',
            self::Delivered => 'Delivered',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
        };
    }
}
