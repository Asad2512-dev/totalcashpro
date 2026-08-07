<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\BillingWebhookEvent;
use App\Services\Billing\StripeWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stripe webhook endpoint (preparation — no live payment processing yet).
 */
final class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly StripeWebhookService $webhookService,
    ) {}

    public function __invoke(Request $request): JsonResponse|Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        if (! $this->webhookService->verifySignature($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        /** @var array<string, mixed> $event */
        $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        $stored = BillingWebhookEvent::query()->firstOrCreate(
            ['external_id' => (string) ($event['id'] ?? '')],
            [
                'provider' => 'stripe',
                'type' => (string) ($event['type'] ?? 'unknown'),
                'payload' => $event,
            ],
        );

        if ($stored->wasRecentlyCreated) {
            $this->webhookService->handle($stored);
        }

        return response()->json(['received' => true]);
    }
}
