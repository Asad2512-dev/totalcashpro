<?php

declare(strict_types=1);

namespace App\Enums;

enum SupplierInvoiceStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
        };
    }
}
