<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AccessRequestStatus;
use App\Enums\SubscriptionPlan;
use App\Models\AccessRequest;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class SuperAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            PlanSeeder::class,
            SettingsSeeder::class,
            CmsSeeder::class,
        ]);
    }

    public function test_super_admin_can_create_organization(): void
    {
        Mail::fake();

        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('super-admin.organizations.store'), [
                'name' => 'Harbour Retail Group',
                'email' => 'ops@harbour.test',
                'owner_name' => 'Ava Morgan',
                'owner_email' => 'ava@harbour.test',
                'country' => 'GB',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'status' => 'active',
            ])
            ->assertRedirect()
            ->assertSessionHas('owner_credentials.email', 'ava@harbour.test');

        $this->assertDatabaseHas('organizations', ['name' => 'Harbour Retail Group']);
        $this->assertDatabaseHas('users', [
            'email' => 'ava@harbour.test',
            'name' => 'Ava Morgan',
        ]);
        $organization = Organization::query()->where('name', 'Harbour Retail Group')->firstOrFail();
        $this->assertNotNull($organization->owner_user_id);
        $this->assertDatabaseHas('activity_logs', ['event' => 'organization.created']);
        Mail::assertSent(\App\Mail\AccessCredentialsMail::class);
    }

    public function test_super_admin_can_approve_access_request(): void
    {
        Mail::fake();

        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $request = AccessRequest::query()->create([
            'business_name' => 'Oak Street Bakery',
            'owner_name' => 'Tom Reed',
            'email' => 'tom@oakstreet.test',
            'phone' => '+44 7700 900111',
            'country' => 'GB',
            'business_type' => 'Bakery',
            'number_of_employees' => '1-5',
            'selected_plan' => SubscriptionPlan::Basic,
            'status' => AccessRequestStatus::Pending,
        ]);

        $this->actingAs($admin)
            ->post(route('super-admin.business-requests.approve', $request))
            ->assertRedirect();

        $this->assertDatabaseHas('organizations', ['name' => 'Oak Street Bakery']);
        $this->assertDatabaseHas('users', ['email' => 'tom@oakstreet.test']);
        $this->assertDatabaseHas('subscriptions', [
            'plan_id' => Plan::query()->where('slug', 'basic')->value('id'),
        ]);
        $this->assertSame(AccessRequestStatus::Approved, $request->fresh()->status);
    }

    public function test_super_admin_can_create_and_update_plan(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('super-admin.plans.store'), [
                'name' => 'Starter Plus',
                'slug' => 'starter-plus',
                'price_monthly' => 24.99,
                'currency' => 'GBP',
                'billing_interval' => 'monthly',
                'features' => "Cash up\nAttendance",
                'is_active' => '1',
                'is_public' => '1',
            ])
            ->assertRedirect(route('super-admin.plans'));

        $plan = Plan::query()->where('slug', 'starter-plus')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('super-admin.plans.update', $plan), [
                'name' => 'Starter Plus',
                'slug' => 'starter-plus',
                'price_monthly' => 22.99,
                'currency' => 'GBP',
                'billing_interval' => 'monthly',
                'features' => "Cash up",
                'is_active' => '1',
                'is_public' => '1',
            ])
            ->assertRedirect(route('super-admin.plans'));

        $this->assertSame('22.99', (string) $plan->fresh()->price_monthly);
    }

    public function test_settings_can_be_saved(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('super-admin.settings.update'), [
                'settings' => [
                    'general' => [
                        'platform_name' => 'TotalCashPro Ops',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'group' => 'general',
            'key' => 'platform_name',
            'value' => 'TotalCashPro Ops',
        ]);
    }

    public function test_global_search_returns_matches(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();
        Organization::factory()->create(['name' => 'Northbridge Kitchen']);

        $this->actingAs($admin)
            ->getJson(route('super-admin.search', ['q' => 'Northbridge']))
            ->assertOk()
            ->assertJsonFragment(['label' => 'Northbridge Kitchen']);
    }
}
