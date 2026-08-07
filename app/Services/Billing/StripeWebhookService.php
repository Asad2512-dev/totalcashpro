<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\BillingWebhookEvent;
use Illuminate\Support\Facades\Log;

/**
 * Stripe webhook handler — architecture preparation only.
 */
final class StripeWebhookService
{
    public function verifySignature(string $payload, string $signature): bool
    {
        $secret = config('billing.stripe.webhook_secret');

        if (blank($secret)) {
            return app()->environment('local', 'testing');
        }

        // Production: implement Stripe signature verification via stripe/stripe-php when integrated.
        return filled($signature);
    }

    public function handle(BillingWebhookEvent $event): void
    {
        Log::info('Stripe webhook received (preparation mode).', [
            'type' => $event->type,
            'external_id' => $event->external_id,
        ]);

        $event->update(['processed_at' => now()]);
    }
}
