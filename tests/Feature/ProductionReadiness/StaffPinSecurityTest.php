<?php

declare(strict_types=1);

namespace Tests\Feature\ProductionReadiness;

use App\Enums\RoleSlug;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Support\Security\StaffPinHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class StaffPinSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_pin_is_hashed_and_not_stored_in_plaintext(): void
    {
        ['admin' => $admin] = $this->makeOrg();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.staff.store'), [
                'name' => 'PIN Staff',
                'email' => 'pin-staff@test.test',
                'pin_code' => '5678',
            ])
            ->assertRedirect(route('business-admin.staff'));

        $staff = User::query()->where('email', 'pin-staff@test.test')->firstOrFail();

        $this->assertNotNull($staff->pin_hash);
        $this->assertNotEquals('5678', $staff->pin_hash);
        $this->assertTrue(StaffPinHasher::verify('5678', $staff->pin_hash));
    }

    public function test_staff_index_does_not_expose_pin_value(): void
    {
        ['admin' => $admin, 'staff' => $staff] = $this->makeOrg();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.staff'))
            ->assertOk()
            ->assertSee('Configured')
            ->assertDontSee('1111');
    }

    /**
     * @return array{admin: User, staff: User}
     */
    private function makeOrg(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'PIN Org',
            'slug' => 'pin-org',
            'email' => 'pin-org@test.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Main',
            'slug' => 'main',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-pin@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $staff = User::query()->create([
            'name' => 'Staff',
            'email' => 'staff-pin@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'pin_hash' => StaffPinHasher::hash('1111'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return compact('admin', 'staff');
    }
}
