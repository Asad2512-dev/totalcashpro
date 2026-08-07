<?php

declare(strict_types=1);

namespace App\Enums;

enum FinanceIntegrationProvider: string
{
    case Stripe = 'stripe';
    case GoCardless = 'gocardless';
    case Xero = 'xero';
    case QuickBooks = 'quickbooks';
    case Sage = 'sage';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::GoCardless => 'GoCardless',
            self::Xero => 'Xero',
            self::QuickBooks => 'QuickBooks',
            self::Sage => 'Sage',
        };
    }
}
