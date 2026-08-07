<?php

declare(strict_types=1);

namespace App\Enums;

enum LeaveType: string
{
    case Holiday = 'holiday';
    case Sick = 'sick';
    case Leave = 'leave';

    public function label(): string
    {
        return match ($this) {
            self::Holiday => 'Holiday',
            self::Sick => 'Sick leave',
            self::Leave => 'Leave',
        };
    }
}
