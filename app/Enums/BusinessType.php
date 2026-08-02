<?php

declare(strict_types=1);

namespace App\Enums;

enum BusinessType: string
{
    case Restaurant = 'restaurant';
    case Cafe = 'cafe';
    case RetailStore = 'retail_store';
    case Bakery = 'bakery';
    case Salon = 'salon';
    case Pharmacy = 'pharmacy';
    case ConvenienceStore = 'convenience_store';
    case FoodTruck = 'food_truck';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Restaurant => 'Restaurant',
            self::Cafe => 'Cafe',
            self::RetailStore => 'Retail Store',
            self::Bakery => 'Bakery',
            self::Salon => 'Salon',
            self::Pharmacy => 'Pharmacy',
            self::ConvenienceStore => 'Convenience Store',
            self::FoodTruck => 'Food Truck',
            self::Other => 'Other',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
