<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CmsSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SuperAdminAuthTest extends TestCase
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

    public function test_super_admin_can_login_and_view_dashboard(): void
    {
        $response = $this->post(route('login.attempt'), [
            'email' => 'admin@totalcashpro.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('super-admin.dashboard'));
        $this->assertAuthenticated();

        $this->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Platform overview', false)
            ->assertSee('Monthly Revenue', false)
            ->assertSee('No businesses yet', false);
    }

    public function test_guest_cannot_access_super_admin(): void
    {
        $this->get(route('super-admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_non_super_admin_cannot_access_panel(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }

    public function test_super_admin_pages_render_from_database(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('super-admin.businesses'))
            ->assertOk()
            ->assertSee('No businesses yet', false);

        $this->actingAs($admin)
            ->get(route('super-admin.users'))
            ->assertOk()
            ->assertSee('admin@totalcashpro.com', false);

        $this->actingAs($admin)
            ->get(route('super-admin.plans'))
            ->assertOk()
            ->assertSee('Professional', false)
            ->assertSee('£29.99', false);

        $this->actingAs($admin)
            ->get(route('super-admin.coupons'))
            ->assertOk()
            ->assertSee('No records yet', false);

        $this->actingAs($admin)
            ->get(route('super-admin.settings'))
            ->assertOk()
            ->assertSee('General settings', false);

        $this->actingAs($admin)
            ->get(route('super-admin.cms.pages'))
            ->assertOk()
            ->assertSee('Home', false);
    }

    public function test_logout_returns_to_login(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_writes_activity_and_audit_logs(): void
    {
        $this->post(route('login.attempt'), [
            'email' => 'admin@totalcashpro.com',
            'password' => 'admin123',
        ])->assertRedirect(route('super-admin.dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'user.login',
            'description' => 'Super Admin signed in',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login',
        ]);
    }
}
