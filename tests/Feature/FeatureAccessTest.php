<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PlanFeature;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use App\Services\Billing\FeatureAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_plan_blocks_inventory_and_allows_cash_up(): void
    {
        $this->seed();

        $plan = Plan::query()->where('slug', 'basic')->firstOrFail();
        $org = Organization::query()->where('slug', 'harbour-kitchen-group')->firstOrFail();
        $subscription = $org->currentSubscription;
        $this->assertNotNull($subscription);
        $subscription->update(['plan_id' => $plan->id]);

        app(FeatureAccessService::class)->forgetOrganization((int) $org->id);

        $admin = User::query()->where('email', 'ava@harbourkitchen.test')->firstOrFail();
        $access = app(FeatureAccessService::class);

        $this->assertTrue($access->can($admin, PlanFeature::CashUp));
        $this->assertFalse($access->can($admin, PlanFeature::Inventory));
        $this->assertFalse($access->can($admin, PlanFeature::Rota));
    }

    public function test_professional_plan_allows_inventory(): void
    {
        $this->seed();

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $org = Organization::query()->where('slug', 'harbour-kitchen-group')->firstOrFail();
        $org->currentSubscription?->update(['plan_id' => $plan->id]);
        app(FeatureAccessService::class)->forgetOrganization((int) $org->id);

        $admin = User::query()->where('email', 'ava@harbourkitchen.test')->firstOrFail();

        $this->assertTrue(app(FeatureAccessService::class)->can($admin, PlanFeature::Inventory));
        $this->assertTrue(app(FeatureAccessService::class)->can($admin, PlanFeature::Payroll));
    }
}
