<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationCategory: string
{
    case Finance = 'finance';
    case Inventory = 'inventory';
    case Payroll = 'payroll';
    case Staff = 'staff';
    case System = 'system';
    case Security = 'security';
    case Support = 'support';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
