<?php

declare(strict_types=1);

namespace App\Enums;

enum CashUpShift: string
{
    case Morning = 'Morning';
    case Evening = 'Evening';

    public function label(): string
    {
        return $this->value;
    }
}
