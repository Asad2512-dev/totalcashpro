<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrganizationStatus;
use App\Enums\PaymentStatus;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SuperAdminDashboardDataTest extends TestCase
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
        ]);
    }

    public function test_dashboard_reflects_real_organization_and_payment_totals(): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $organization = Organization::factory()->create([
            'name' => 'Harbour Retail Group',
            'status' => OrganizationStatus::Active,
            'owner_user_id' => $admin->id,
        ]);

        Payment::factory()->create([
            'organization_id' => $organization->id,
            'amount' => 29.99,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Harbour Retail Group', false)
            ->assertSee('£29.99', false)
            ->assertDontSee('No businesses yet', false);

        $this->actingAs($admin)
            ->get(route('super-admin.businesses'))
            ->assertOk()
            ->assertSee('Harbour Retail Group', false);
    }
}
