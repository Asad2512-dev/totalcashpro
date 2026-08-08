<?php

declare(strict_types=1);

namespace App\Enums;

enum AttendanceSource: string
{
    case Manual = 'manual';
    case Kiosk = 'kiosk';
    case StaffApp = 'staff_app';
    case Admin = 'admin';
}
