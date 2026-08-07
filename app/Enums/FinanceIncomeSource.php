<?php

declare(strict_types=1);

namespace App\Enums;

enum FinanceIncomeSource: string
{
    case Manual = 'manual';
    case CashUp = 'cash_up';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual entry',
            self::CashUp => 'Cash up',
            self::Other => 'Other',
        };
    }
}
