<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;

final class FinanceCatalogService implements ServiceInterface
{
    /**
     * @return array<string, string>
     */
    public function billCategories(): array
    {
        return [
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'insurance' => 'Insurance',
            'software' => 'Software & subscriptions',
            'tax' => 'Tax & compliance',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function expenseCategories(): array
    {
        return [
            'supplies' => 'Supplies',
            'marketing' => 'Marketing',
            'maintenance' => 'Maintenance & repairs',
            'travel' => 'Travel',
            'food' => 'Food & beverage',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function paymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank' => 'Bank transfer',
            'other' => 'Other',
        ];
    }
}
