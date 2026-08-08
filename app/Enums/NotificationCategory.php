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
    case Procurement = 'procurement';
    case Deliveries = 'deliveries';
    case Rota = 'rota';
    case Executive = 'executive';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
