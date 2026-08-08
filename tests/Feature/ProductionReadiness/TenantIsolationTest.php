<?php

declare(strict_types=1);

namespace Tests\Feature\ProductionReadiness;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CashUp;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\User;
use App\Support\Security\StaffPinHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_be_assigned_to_foreign_branch(): void
    {
        ['admin' => $admin, 'foreignBranch' => $foreignBranch] = $this->makeOrgs();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.staff.store'), [
                'name' => 'Cross Org Staff',
                'email' => 'cross@test.test',
                'branch_id' => $foreignBranch->id,
                'pin_code' => '4321',
            ])
            ->assertSessionHasErrors('branch_id');
    }

    public function test_purchase_order_rejects_foreign_supplier(): void
    {
        ['admin' => $admin, 'foreignSupplier' => $foreignSupplier] = $this->makeOrgs(withSuppliersPlan: true);

        $response = $this->actingAsVerified($admin)
            ->post(route('business-admin.purchase-orders.store'), [
                'supplier_id' => $foreignSupplier->id,
                'lines' => [[
                    'description' => 'Test item',
                    'quantity' => 1,
                    'unit_cost' => 10,
                ]],
            ]);

        $response->assertSessionHasErrors('supplier_id');
    }

    public function test_staff_update_rejects_foreign_branch(): void
    {
        ['admin' => $admin, 'staff' => $staff, 'foreignBranch' => $foreignBranch] = $this->makeOrgs();

        $this->actingAsVerified($admin)
            ->put(route('business-admin.staff.update', $staff), [
                'name' => $staff->name,
                'email' => $staff->email,
                'branch_id' => $foreignBranch->id,
            ])
            ->assertSessionHasErrors('branch_id');
    }

    public function test_cash_history_rejects_foreign_cash_up(): void
    {
        ['admin' => $admin, 'foreignBranch' => $foreignBranch] = $this->makeOrgs();
        $foreignOrg = $foreignBranch->organization;

        $foreignAdmin = User::query()->create([
            'name' => 'Foreign Admin',
            'email' => 'foreign-admin@test.test',
            'password' => Hash::make('password'),
            'role_id' => Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail()->id,
            'organization_id' => $foreignOrg->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $foreignCashUp = CashUp::query()->create([
            'organization_id' => $foreignOrg->id,
            'branch_id' => $foreignBranch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'coins_total' => 0,
            'notes_total' => 0,
            'cards_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $foreignAdmin->id,
            'status' => 'submitted',
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-history.show', $foreignCashUp->id))
            ->assertNotFound();
    }

  /**
     * @return array{admin: User, staff?: User, foreignBranch: Branch, foreignSupplier: Supplier}
     */
    private function makeOrgs(bool $withSuppliersPlan = false): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        if ($withSuppliersPlan) {
            $this->seed(\Database\Seeders\PlanSeeder::class);
        }

        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $orgA = Organization::query()->create([
            'name' => 'Org A',
            'slug' => 'org-a',
            'email' => 'a@test.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        if ($withSuppliersPlan) {
            $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
            Subscription::query()->create([
                'organization_id' => $orgA->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active->value,
                'starts_at' => now()->subMonth(),
                'current_period_start' => now()->subMonth(),
                'current_period_end' => now()->addMonth(),
            ]);
        }

        $orgB = Organization::query()->create([
            'name' => 'Org B',
            'slug' => 'org-b',
            'email' => 'b@test.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branchA = Branch::query()->create([
            'organization_id' => $orgA->id,
            'name' => 'Branch A',
            'slug' => 'branch-a',
            'status' => 'open',
        ]);

        $foreignBranch = Branch::query()->create([
            'organization_id' => $orgB->id,
            'name' => 'Branch B',
            'slug' => 'branch-b',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Admin A',
            'email' => 'admin-a@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $orgA->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $staff = User::query()->create([
            'name' => 'Staff A',
            'email' => 'staff-a@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $orgA->id,
            'branch_id' => $branchA->id,
            'pin_hash' => StaffPinHasher::hash('1111'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $foreignSupplier = Supplier::query()->create([
            'organization_id' => $orgB->id,
            'branch_id' => $foreignBranch->id,
            'name' => 'Foreign Supplier',
            'status' => 'active',
        ]);

        return compact('admin', 'staff', 'foreignBranch', 'foreignSupplier');
    }
}
