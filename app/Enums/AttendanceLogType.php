<?php

declare(strict_types=1);

namespace App\Enums;

enum AttendanceLogType: string
{
    case ClockIn = 'clock-in';
    case ClockOut = 'clock-out';
}
