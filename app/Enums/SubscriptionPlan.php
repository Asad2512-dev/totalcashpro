<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionPlan: string
{
    case Basic = 'basic';
    case Professional = 'professional';

    public function label(): string
    {
        return match ($this) {
            self::Basic => 'Basic',
            self::Professional => 'Professional',
        };
    }

    public function priceLabel(): string
    {
        return match ($this) {
            self::Basic => '£19.99/month',
            self::Professional => '£29.99/month',
        };
    }
}
