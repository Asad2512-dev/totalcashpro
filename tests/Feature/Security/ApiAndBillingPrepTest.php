<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\SecurityLogEvent;
use App\Models\BillingWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class ApiAndBillingPrepTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_authenticated_user_can_list_api_tokens(): void
    {
        $user = $this->makeBusinessAdmin();
        Sanctum::actingAs($user);

        $this->getJson('/api/tokens')->assertOk()->assertJsonStructure(['tokens']);
    }

    public function test_authenticated_user_can_create_api_token(): void
    {
        $user = $this->makeBusinessAdmin();
        Sanctum::actingAs($user);

        $this->postJson('/api/tokens', ['name' => 'Mobile App'])
            ->assertCreated()
            ->assertJsonStructure(['token', 'id']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Mobile App',
        ]);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::ApiTokenCreated->value,
        ]);
    }

    public function test_authenticated_user_can_revoke_api_token(): void
    {
        $user = $this->makeBusinessAdmin();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/tokens', ['name' => 'Temp'])->json('id');

        $this->deleteJson('/api/tokens/'.$created)->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $created,
        ]);
    }

    public function test_guest_cannot_access_api_tokens(): void
    {
        $this->getJson('/api/tokens')->assertUnauthorized();
    }

    public function test_stripe_webhook_stores_event_in_testing(): void
    {
        $payload = [
            'id' => 'evt_test_123',
            'type' => 'customer.subscription.updated',
            'data' => ['object' => ['id' => 'sub_123']],
        ];

        $this->postJson(route('webhooks.stripe'), $payload, [
            'Stripe-Signature' => 'test_sig',
        ])->assertOk();

        $this->assertDatabaseHas('billing_webhook_events', [
            'external_id' => 'evt_test_123',
            'type' => 'customer.subscription.updated',
        ]);
    }

    public function test_stripe_webhook_is_idempotent(): void
    {
        $payload = [
            'id' => 'evt_test_dup',
            'type' => 'invoice.paid',
            'data' => [],
        ];

        $this->postJson(route('webhooks.stripe'), $payload, ['Stripe-Signature' => 'sig']);
        $this->postJson(route('webhooks.stripe'), $payload, ['Stripe-Signature' => 'sig']);

        $this->assertSame(1, BillingWebhookEvent::query()->where('external_id', 'evt_test_dup')->count());
    }
}
