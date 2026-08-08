<?php

declare(strict_types=1);

namespace App\Enums;

enum KioskSyncEventType: string
{
    case ClockIn = 'CLOCK_IN';
    case ClockOut = 'CLOCK_OUT';
    case BreakStart = 'BREAK_START';
    case BreakEnd = 'BREAK_END';
}
