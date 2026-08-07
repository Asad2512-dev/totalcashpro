<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceIntegrationProvider;
use App\Models\FinanceIntegrationConnection;
use App\Models\User;

/**
 * Stub service preparing future Stripe, GoCardless, Xero, QuickBooks and Sage hooks.
 */
final class FinanceIntegrationService implements ServiceInterface
{
    /**
     * @return list<array{provider: FinanceIntegrationProvider, status: string, connected_at: ?string}>
     */
    public function listFor(User $user): array
    {
        $existing = FinanceIntegrationConnection::query()
            ->where('organization_id', $user->organization_id)
            ->get()
            ->keyBy(fn (FinanceIntegrationConnection $c) => $c->provider->value);

        return array_map(function (FinanceIntegrationProvider $provider) use ($existing): array {
            $connection = $existing->get($provider->value);

            return [
                'provider' => $provider,
                'status' => $connection?->status ?? 'disconnected',
                'connected_at' => $connection?->connected_at?->toDateTimeString(),
            ];
        }, FinanceIntegrationProvider::cases());
    }
}
