<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DeliveryStatus;
use App\Enums\InventoryStocktakeStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\RoleSlug;
use App\Models\Branch;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryStocktake;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Services\BusinessAdmin\InventoryReplenishmentService;
use App\Services\BusinessAdmin\InventoryStocktakeService;
use App\Services\BusinessAdmin\PurchaseOrderService;
use App\Services\BusinessAdmin\RiderDeliveryService;
use App\Services\BusinessAdmin\RiderService;
use App\Services\BusinessAdmin\VendorOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Phase4InventoryTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_on_order_quantity_reduces_suggested_replenishment(): void
    {
        $service = app(InventoryReplenishmentService::class);

        $suggested = $service->suggestedOrder(
            parLevel: 100,
            currentStock: 40,
            onOrder: 30,
        );

        $this->assertEquals(30.0, $suggested);
        $this->assertNotEquals(60.0, $suggested);
    }

    public function test_goods_receiving_increases_inventory_by_accepted_quantity_only(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $supplier = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Fresh Foods',
        ]);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Chicken',
            'packaging' => 'kg',
            'unit' => 'kg',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 50,
            'par_level' => 100,
            'cost_price' => 5,
        ]);

        $po = PurchaseOrder::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-TEST-001',
            'status' => PurchaseOrderStatus::Ordered,
            'subtotal' => 500,
            'vat_total' => 100,
            'total' => 600,
            'created_by' => $admin->id,
        ]);

        $line = $po->lines()->create([
            'inventory_item_id' => $item->id,
            'description' => 'Chicken',
            'quantity' => 100,
            'unit_cost' => 5,
            'line_total' => 500,
        ]);

        session(['business_admin.branch_id' => $branch->id]);

        app(PurchaseOrderService::class)->receiveGoods($admin, $po, [
            [
                'purchase_order_line_id' => $line->id,
                'quantity_received' => 80,
                'quantity_damaged' => 5,
                'quantity_missing' => 15,
            ],
        ]);

        $item->refresh();
        $this->assertEquals(125, $item->stock_total_pcs);
        $this->assertNotEquals(150, $item->stock_total_pcs);
        $this->assertNotEquals(130, $item->stock_total_pcs);
    }

    public function test_approved_stocktake_splits_orders_by_vendor(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        $vendorA = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Vendor A',
        ]);
        $vendorB = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Vendor B',
        ]);

        $chicken = $this->makeItem($admin, $branch, 'Chicken', $vendorA, 40, 100);
        $bread = $this->makeItem($admin, $branch, 'Bread', $vendorB, 20, 50);

        $stocktake = InventoryStocktake::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek(),
            'week_end' => now()->endOfWeek(),
            'status' => InventoryStocktakeStatus::UnderReview->value,
            'created_by' => $admin->id,
        ]);

        $stocktake->items()->create([
            'inventory_item_id' => $chicken->id,
            'system_qty' => 60,
            'counted_qty' => 60,
            'difference_qty' => 0,
            'par_level' => 100,
            'on_order_qty' => 0,
            'suggested_order_qty' => 40,
            'ordered_qty' => 40,
            'supplier_id' => $vendorA->id,
        ]);

        $stocktake->items()->create([
            'inventory_item_id' => $bread->id,
            'system_qty' => 30,
            'counted_qty' => 30,
            'difference_qty' => 0,
            'par_level' => 50,
            'on_order_qty' => 0,
            'suggested_order_qty' => 20,
            'ordered_qty' => 20,
            'supplier_id' => $vendorB->id,
        ]);

        app(InventoryStocktakeService::class)->approve($admin, $stocktake);

        $pos = PurchaseOrder::query()->where('notes', 'like', '%stocktake%')->get();
        $this->assertCount(2, $pos);
        $this->assertEqualsCanonicalizing(
            [$vendorA->id, $vendorB->id],
            $pos->pluck('supplier_id')->all(),
        );
    }

    public function test_staff_stocktake_workflow(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $staffRole = \App\Models\Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $staff = User::query()->create([
            'name' => 'Stock Staff',
            'email' => 'stock-staff@example.com',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Tomatoes',
            'packaging' => 'kg',
            'unit' => 'kg',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 18,
            'par_level' => 25,
            'stock_limit' => 25,
        ]);

        $this->actingAsVerified($staff)
            ->get(route('staff.stocktake'))
            ->assertOk()
            ->assertSee('Weekly stocktake')
            ->assertSee('Tomatoes');
    }

    public function test_rider_assignment_and_delivery_advance(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        $riderProfile = app(RiderService::class)->create($admin, [
            'name' => 'Rider One',
            'email' => 'rider-one@example.com',
            'branch_id' => $branch->id,
        ]);

        $supplier = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Supplier',
        ]);

        $po = PurchaseOrder::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-RIDER-1',
            'status' => PurchaseOrderStatus::Ordered,
            'subtotal' => 10,
            'vat_total' => 2,
            'total' => 12,
            'created_by' => $admin->id,
        ]);

        $delivery = app(RiderDeliveryService::class)->assign($admin, $po, $riderProfile);
        $this->assertEquals(DeliveryStatus::Assigned, $delivery->status);

        $advanced = app(RiderDeliveryService::class)->advanceStatus(
            $riderProfile,
            $delivery,
            DeliveryStatus::Accepted,
        );
        $this->assertEquals(DeliveryStatus::Accepted, $advanced->status);
    }

    public function test_admin_override_requires_reason(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $stocktake = InventoryStocktake::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek(),
            'week_end' => now()->endOfWeek(),
            'status' => InventoryStocktakeStatus::UnderReview->value,
            'created_by' => $admin->id,
        ]);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Milk',
            'packaging' => 'l',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 10,
        ]);

        $line = $stocktake->items()->create([
            'inventory_item_id' => $item->id,
            'system_qty' => 10,
            'counted_qty' => 10,
            'par_level' => 30,
            'suggested_order_qty' => 20,
            'supplier_id' => null,
            'excluded_from_order' => true,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(InventoryStocktakeService::class)->approve($admin, $stocktake, [
            [
                'inventory_stocktake_item_id' => $line->id,
                'ordered_qty' => 50,
                'excluded_from_order' => false,
            ],
        ]);
    }

    private function makeItem($admin, Branch $branch, string $name, Supplier $supplier, int $stock, int $par): InventoryItem
    {
        return InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => $name,
            'supplier_id' => $supplier->id,
            'packaging' => 'pcs',
            'unit' => 'pcs',
            'pcs_per_box' => 1,
            'stock_total_pcs' => $stock,
            'par_level' => $par,
            'stock_limit' => $par,
            'cost_price' => 1,
        ]);
    }
}
