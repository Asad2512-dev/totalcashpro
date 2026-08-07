<?php

declare(strict_types=1);

namespace App\Contracts\Finance\Integrations;

use App\Enums\FinanceIntegrationProvider;

/**
 * Future integration hook for Stripe, GoCardless, Xero, QuickBooks and Sage.
 */
interface FinanceIntegrationDriverInterface
{
    public function provider(): FinanceIntegrationProvider;

    /**
     * @param  array<string, mixed>  $settings
     */
    public function connect(array $settings): bool;

    public function disconnect(): bool;

    public function sync(): void;
}
