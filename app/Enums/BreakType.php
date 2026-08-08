<?php

declare(strict_types=1);

namespace App\Enums;

enum BreakType: string
{
    case Lunch = 'lunch';
    case Namaz = 'namaz';
    case Jumma = 'jumma';
    case Personal = 'personal';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Lunch => 'Lunch',
            self::Namaz => 'Namaz',
            self::Jumma => 'Jumma',
            self::Personal => 'Personal',
            self::Other => 'Other',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        return match ($this) {
            self::Lunch => ['enabled' => true, 'default_minutes' => 30, 'max_minutes' => 60, 'paid' => false, 'allow_multiple' => false],
            self::Namaz => ['enabled' => true, 'default_minutes' => 15, 'max_minutes' => 30, 'paid' => true, 'allow_multiple' => true],
            self::Jumma => ['enabled' => true, 'default_minutes' => 60, 'max_minutes' => 90, 'paid' => true, 'allow_multiple' => false],
            self::Personal => ['enabled' => true, 'default_minutes' => 15, 'max_minutes' => 30, 'paid' => false, 'allow_multiple' => true],
            self::Other => ['enabled' => true, 'default_minutes' => 15, 'max_minutes' => 60, 'paid' => false, 'allow_multiple' => true],
        };
    }
}
