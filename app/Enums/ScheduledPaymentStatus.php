<?php

declare(strict_types=1);

namespace App\Enums;

enum ScheduledPaymentStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Failed = 'failed';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
