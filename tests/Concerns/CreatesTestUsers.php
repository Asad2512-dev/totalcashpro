<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait CreatesTestUsers
{
    protected function seedRolesAndPlans(): void
    {
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\PlanSeeder::class,
        ]);
    }

    protected function makeBusinessAdmin(string $email = 'owner@example.com'): User
    {
        $this->seedRolesAndPlans();

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Test Biz',
            'slug' => 'test-biz-'.uniqid(),
            'email' => 'biz@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main',
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

        return $admin->fresh(['role']);
    }

    protected function strongPassword(): string
    {
        return 'Str0ng!Pass#word';
    }
}
