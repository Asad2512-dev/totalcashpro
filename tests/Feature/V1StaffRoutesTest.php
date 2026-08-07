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
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class V1StaffRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('staffGetRoutes')]
    public function test_staff_get_route_is_accessible(string $routeName): void
    {
        $staff = $this->makeStaffUser();

        $this->actingAsVerified($staff)
            ->get(route($routeName))
            ->assertOk();
    }

    public static function staffGetRoutes(): array
    {
        return [
            ['staff.dashboard'],
            ['staff.clock'],
            ['staff.cash-up'],
            ['staff.shift'],
            ['staff.shift-swap'],
            ['staff.attendance'],
            ['staff.hours'],
            ['staff.availability'],
            ['staff.leave'],
            ['staff.notifications'],
            ['staff.profile'],
        ];
    }

    private function makeStaffUser(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Staff Biz',
            'slug' => 'staff-biz',
            'email' => 'biz-staff@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main-staff',
            'status' => 'open',
        ]);

        Subscription::query()->create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        return User::query()->create([
            'name' => 'Staff User',
            'email' => 'staff-user@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'branch_id' => $branch->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('1234'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
