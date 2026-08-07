<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class HrModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_page_loads(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.hr'))
            ->assertOk()
            ->assertSee('Leave Requests');
    }

    public function test_hr_page_shows_shift_swap_section(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.hr'))
            ->assertOk()
            ->assertSee('Shift swap requests', false);
    }

    private function makeBusinessAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Test Biz',
            'slug' => 'test-biz-hr',
            'email' => 'owner-hr@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main-hr',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner-hr@example.com',
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
