<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportCompareMode: string
{
    case None = 'none';
    case PreviousPeriod = 'previous_period';
    case Branch = 'branch';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::None => 'No comparison',
            self::PreviousPeriod => 'Previous period',
            self::Branch => 'Compare branch',
            self::Employee => 'Compare employee',
        };
    }
}
