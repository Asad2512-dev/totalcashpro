<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CashDrawerStatus;
use App\Enums\CashUpStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\CashUp;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BusinessAdmin\CashDrawerService;
use App\Services\BusinessAdmin\CashMovementService;
use App\Services\BusinessAdmin\CashUpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class TillManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_two_tills_for_dockside_with_default_float(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();

        /** @var CashDrawerService $service */
        $service = app(CashDrawerService::class);

        $till1 = $service->create($admin, [
            'name' => 'Till 1',
            'code' => 'TILL-01',
            'branch_id' => $dockside->id,
            'default_opening_float' => 100,
        ]);

        $till2 = $service->create($admin, [
            'name' => 'Till 2',
            'code' => 'TILL-02',
            'branch_id' => $dockside->id,
            'default_opening_float' => 100,
        ]);

        $this->assertEquals(100.0, $till1->defaultOpeningFloat());
        $this->assertEquals(100.0, $till2->defaultOpeningFloat());
        $this->assertDatabaseCount('cash_drawers', 2);
    }

    public function test_duplicate_till_code_in_same_branch_fails(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $service = app(CashDrawerService::class);

        $service->create($admin, ['name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $dockside->id]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $service->create($admin, ['name' => 'Till 1B', 'code' => 'TILL-01', 'branch_id' => $dockside->id]);
    }

    public function test_same_code_allowed_in_different_branches(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'central' => $central] = $this->harbourKitchen();
        $service = app(CashDrawerService::class);

        $service->create($admin, ['name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $dockside->id]);
        $service->create($admin, ['name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $central->id]);

        $this->assertDatabaseCount('cash_drawers', 2);
    }

    public function test_independent_cash_ups_per_till_same_shift(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $service = app(CashDrawerService::class);
        $cashUps = app(CashUpService::class);

        $till1 = $service->create($admin, ['name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $dockside->id]);
        $till2 = $service->create($admin, ['name' => 'Till 2', 'code' => 'TILL-02', 'branch_id' => $dockside->id]);

        session(['business_admin.branch_id' => $dockside->id]);

        $payload = fn (int $drawerId, int $noteQty) => [
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'cash_drawer_id' => $drawerId,
            'opening_float' => 100,
            'coins' => [],
            'notes' => [['note' => '£50', 'qty' => $noteQty, 'is_qty' => true]],
            'cards' => [],
            'expenses' => [],
            'online' => [],
        ];

        $cu1 = $cashUps->save($admin, $payload($till1->id, 11), true);
        $cu2 = $cashUps->save($admin, $payload($till2->id, 5), true);

        $this->assertNotSame($cu1->id, $cu2->id);
        $this->assertEquals($till1->id, $cu1->cash_drawer_id);
        $this->assertEquals($till2->id, $cu2->cash_drawer_id);
    }

    public function test_expected_cash_excludes_cards_from_physical_till(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $drawer = app(CashDrawerService::class)->create($admin, [
            'name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $dockside->id,
        ]);

        session(['business_admin.branch_id' => $dockside->id]);

        $cashUp = app(CashUpService::class)->save($admin, [
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'cash_drawer_id' => $drawer->id,
            'opening_float' => 100,
            'coins' => [],
            'notes' => [['note' => '£50', 'qty' => 8, 'is_qty' => true]],
            'cards' => [['payment_type' => 'Card', 'type' => 'machine', 'amount' => 200]],
            'expenses' => [['description' => 'Supplies', 'amount' => 50]],
            'online' => [],
        ], true);

        $this->assertEquals(350.0, (float) $cashUp->cash_sales_total);
        $this->assertEquals(400.0, $cashUp->calculatedExpectedCash());
        $this->assertEquals(200.0, (float) $cashUp->cards_total);
    }

    public function test_transfer_between_tills_same_branch(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $drawers = app(CashDrawerService::class);
        $till1 = $drawers->create($admin, ['name' => 'Till 1', 'code' => 'TILL-01', 'branch_id' => $dockside->id, 'default_opening_float' => 200]);
        $till2 = $drawers->create($admin, ['name' => 'Till 2', 'code' => 'TILL-02', 'branch_id' => $dockside->id, 'default_opening_float' => 100]);

        app(CashMovementService::class)->transfer($admin, $till1, $till2, 50, 'Change float');

        $this->assertEquals(150.0, (float) $till1->refresh()->current_balance);
        $this->assertEquals(150.0, (float) $till2->refresh()->current_balance);
    }

    public function test_cross_branch_transfer_fails(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'central' => $central] = $this->harbourKitchen();
        $drawers = app(CashDrawerService::class);
        $till1 = $drawers->create($admin, ['name' => 'Till 1', 'branch_id' => $dockside->id]);
        $till2 = $drawers->create($admin, ['name' => 'Till 2', 'branch_id' => $central->id]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(CashMovementService::class)->transfer($admin, $till1, $till2, 50, 'Invalid');
    }

    public function test_deactivated_till_cannot_be_used_for_cash_up(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $drawers = app(CashDrawerService::class);
        $till = $drawers->create($admin, ['name' => 'Till 1', 'branch_id' => $dockside->id]);
        $drawers->setStatus($admin, $till, CashDrawerStatus::Inactive);

        session(['business_admin.branch_id' => $dockside->id]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(CashUpService::class)->save($admin, [
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'cash_drawer_id' => $till->id,
            'opening_float' => 100,
            'coins' => [], 'notes' => [], 'cards' => [], 'expenses' => [], 'online' => [],
        ]);
    }

    public function test_historical_cash_up_on_inactive_till_remains_visible(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $drawers = app(CashDrawerService::class);
        $till = $drawers->create($admin, ['name' => 'Till 1', 'branch_id' => $dockside->id]);
        $drawers->setStatus($admin, $till, CashDrawerStatus::Inactive);

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $dockside->id,
            'cash_drawer_id' => $till->id,
            'cashup_date' => now()->subDay()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 100,
            'coins_total' => 200,
            'status' => CashUpStatus::Approved->value,
            'created_by' => $admin->id,
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-history.show', $cashUp))
            ->assertOk()
            ->assertSee('Till 1');
    }

    public function test_opening_float_change_does_not_alter_historical_cash_up(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $drawers = app(CashDrawerService::class);
        $till = $drawers->create($admin, ['name' => 'Till 1', 'branch_id' => $dockside->id, 'default_opening_float' => 100]);

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $dockside->id,
            'cash_drawer_id' => $till->id,
            'cashup_date' => now()->subWeek()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 100,
            'created_by' => $admin->id,
        ]);

        $drawers->update($admin, $till, ['default_opening_float' => 150]);

        $this->assertEquals(100.0, (float) $cashUp->fresh()->opening_float);
    }

    public function test_till_management_page_loads(): void
    {
        ['admin' => $admin] = $this->harbourKitchen();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-drawers'))
            ->assertOk()
            ->assertSee('Till management');
    }

    public function test_cash_up_print_shows_till(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->harbourKitchen();
        $till = app(CashDrawerService::class)->create($admin, ['name' => 'Till 1', 'branch_id' => $dockside->id]);

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $dockside->id,
            'cash_drawer_id' => $till->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Evening',
            'opening_float' => 100,
            'expected_cash' => 550,
            'actual_cash' => 545,
            'variance' => -5,
            'created_by' => $admin->id,
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-up.print', $cashUp))
            ->assertOk()
            ->assertSee('Till 1')
            ->assertSee('Opening float');
    }

    /**
     * @return array<string, mixed>
     */
    private function harbourKitchen(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'Harbour Kitchen Group',
            'slug' => 'harbour-kitchen-'.uniqid(),
            'email' => 'admin@harbour.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $dockside = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Dockside',
            'slug' => 'dockside',
            'status' => 'open',
        ]);

        $central = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Harbour Central',
            'slug' => 'harbour-central',
            'status' => 'open',
        ]);

        Subscription::query()->create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subMonth(),
            'current_period_start' => now()->subMonth(),
            'current_period_end' => now()->addMonth(),
        ]);

        $admin = User::query()->create([
            'name' => 'Harbour Admin',
            'email' => 'admin-harbour-'.uniqid().'@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $admin->id]);

        return compact('org', 'admin', 'dockside', 'central');
    }
}
