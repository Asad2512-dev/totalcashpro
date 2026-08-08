<?php

declare(strict_types=1);

namespace App\Enums;

enum AlertPriority: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
