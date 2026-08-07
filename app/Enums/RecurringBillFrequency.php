<?php

declare(strict_types=1);

namespace App\Enums;

enum RecurringBillFrequency: string
{
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';
}
