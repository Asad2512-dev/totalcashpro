<?php

declare(strict_types=1);

namespace App\Enums;

enum WageStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
}
