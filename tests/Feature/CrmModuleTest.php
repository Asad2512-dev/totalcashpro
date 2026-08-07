<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CrmCustomer;
use App\Models\CrmCustomerNote;
use App\Models\CrmCustomerVisit;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class CrmModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_index_and_create_customer(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.crm'))
            ->assertOk()
            ->assertSee('Customers');

        $this->actingAsVerified($admin)
            ->post(route('business-admin.crm.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '07700900123',
                'notes' => 'Regular customer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_customers', [
            'organization_id' => $admin->organization_id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_crm_customer_detail_update_note_and_visit(): void
    {
        $admin = $this->makeBusinessAdmin();
        $customer = $this->makeCustomer($admin);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.crm.show', $customer))
            ->assertOk()
            ->assertSee('Jane Doe');

        $this->actingAsVerified($admin)
            ->put(route('business-admin.crm.update', $customer), [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'marketing_email' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('Jane Smith', $customer->fresh()->name);

        $this->actingAsVerified($admin)
            ->post(route('business-admin.crm.notes.store', $customer), [
                'body' => 'Called about booking',
            ])
            ->assertRedirect();

        $this->assertSame(1, CrmCustomerNote::query()->where('crm_customer_id', $customer->id)->count());

        $this->actingAsVerified($admin)
            ->post(route('business-admin.crm.visits.store', $customer), [
                'spend_amount' => 45.50,
                'notes' => 'Lunch visit',
            ])
            ->assertRedirect();

        $this->assertSame(1, CrmCustomerVisit::query()->where('crm_customer_id', $customer->id)->count());
    }

    public function test_crm_search_filter(): void
    {
        $admin = $this->makeBusinessAdmin();
        $this->makeCustomer($admin);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.crm', ['q' => 'Jane']))
            ->assertOk()
            ->assertSee('Jane Doe');
    }

    public function test_crm_delete_customer(): void
    {
        $admin = $this->makeBusinessAdmin();
        $customer = $this->makeCustomer($admin);

        $this->actingAsVerified($admin)
            ->delete(route('business-admin.crm.destroy', $customer))
            ->assertRedirect(route('business-admin.crm'));

        $this->assertDatabaseMissing('crm_customers', ['id' => $customer->id]);
    }

    public function test_other_org_cannot_view_customer(): void
    {
        $admin = $this->makeBusinessAdmin();
        $other = $this->makeBusinessAdmin('other@example.com', 'other-biz');
        $customer = $this->makeCustomer($admin);

        $this->actingAsVerified($other)
            ->get(route('business-admin.crm.show', $customer))
            ->assertForbidden();
    }

    private function makeCustomer(User $admin): CrmCustomer
    {
        return CrmCustomer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $admin->organization->branches()->first()?->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'marketing_preferences' => ['email' => false, 'sms' => false],
        ]);
    }

    private function makeBusinessAdmin(string $email = 'owner@example.com', string $slug = 'test-biz'): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Test Biz',
            'slug' => $slug,
            'email' => $email,
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => $slug.'-main',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Owner',
            'email' => $email,
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $organization->update(['owner_user_id' => $admin->id]);

        Subscription::query()->create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        return $admin->fresh();
    }
}
