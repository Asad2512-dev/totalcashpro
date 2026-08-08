<?php

declare(strict_types=1);

namespace App\Enums;

enum BudgetCategory: string
{
    case Revenue = 'revenue';
    case FoodCost = 'food_cost';
    case Wages = 'wages';
    case Utilities = 'utilities';
    case Rent = 'rent';
    case Marketing = 'marketing';
    case Other = 'other_expenses';

    public function label(): string
    {
        return match ($this) {
            self::Revenue => 'Revenue',
            self::FoodCost => 'Food cost',
            self::Wages => 'Wages',
            self::Utilities => 'Utilities',
            self::Rent => 'Rent',
            self::Marketing => 'Marketing',
            self::Other => 'Other expenses',
        };
    }
}
