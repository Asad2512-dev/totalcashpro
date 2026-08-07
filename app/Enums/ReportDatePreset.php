<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportDatePreset: string
{
    case Today = 'today';
    case Yesterday = 'yesterday';
    case Last7Days = 'last_7_days';
    case Last30Days = 'last_30_days';
    case ThisWeek = 'this_week';
    case LastWeek = 'last_week';
    case ThisMonth = 'this_month';
    case LastMonth = 'last_month';
    case ThisQuarter = 'this_quarter';
    case ThisYear = 'this_year';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Today => 'Today',
            self::Yesterday => 'Yesterday',
            self::Last7Days => 'Last 7 days',
            self::Last30Days => 'Last 30 days',
            self::ThisWeek => 'This week',
            self::LastWeek => 'Last week',
            self::ThisMonth => 'This month',
            self::LastMonth => 'Last month',
            self::ThisQuarter => 'This quarter',
            self::ThisYear => 'This year',
            self::Custom => 'Custom range',
        };
    }
}
