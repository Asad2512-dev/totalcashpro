<?php

declare(strict_types=1);

namespace App\Enums;

enum CashDrawerSessionStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
    case Reconciled = 'reconciled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
            self::Reconciled => 'Reconciled',
        };
    }
}
