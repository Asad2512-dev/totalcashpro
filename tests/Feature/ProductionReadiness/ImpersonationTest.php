<?php

declare(strict_types=1);

namespace Tests\Feature\ProductionReadiness;

use App\Enums\RoleSlug;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_impersonate_with_reason_and_exit(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $superRole = Role::query()->where('slug', RoleSlug::SuperAdmin->value)->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();

        $super = User::query()->create([
            'name' => 'Super',
            'email' => 'super@test.test',
            'password' => Hash::make('password'),
            'role_id' => $superRole->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $org = Organization::query()->create([
            'name' => 'Demo Org',
            'slug' => 'demo-org',
            'email' => 'demo@test.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $owner = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $owner->id]);

        $this->actingAsVerified($super)
            ->post(route('super-admin.organizations.login-as', $org), [
                'reason' => 'Support ticket investigation',
            ])
            ->assertRedirect(route('business-admin.dashboard'));

        $this->assertAuthenticatedAs($owner);
        $this->assertDatabaseHas('audit_logs', ['action' => 'impersonation.started']);

        $this->post(route('impersonation.stop'))
            ->assertRedirect(route('super-admin.dashboard'));

        $this->assertAuthenticatedAs($super);
        $this->assertDatabaseHas('audit_logs', ['action' => 'impersonation.stopped']);
    }

    public function test_login_as_requires_reason(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $superRole = Role::query()->where('slug', RoleSlug::SuperAdmin->value)->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();

        $super = User::query()->create([
            'name' => 'Super',
            'email' => 'super2@test.test',
            'password' => Hash::make('password'),
            'role_id' => $superRole->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $org = Organization::query()->create([
            'name' => 'Demo Org 2',
            'slug' => 'demo-org-2',
            'email' => 'demo2@test.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $owner = User::query()->create([
            'name' => 'Owner 2',
            'email' => 'owner2@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $org->update(['owner_user_id' => $owner->id]);

        $this->actingAsVerified($super)
            ->post(route('super-admin.organizations.login-as', $org), [])
            ->assertSessionHasErrors('reason');

        $this->assertFalse(AuditLog::query()->where('action', 'impersonation.started')->exists());
    }
}
