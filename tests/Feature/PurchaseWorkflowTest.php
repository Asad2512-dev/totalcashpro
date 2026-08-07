<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PurchaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_purchase_workflow_updates_stock_and_finance(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'ava@harbourkitchen.test')->firstOrFail();
        $branchId = $admin->organization->branches()->first()->id;

        $supplier = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'name' => 'Test Supplier',
        ]);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'name' => 'Test Item',
            'packaging' => 'pcs',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 5,
            'stock_limit' => 10,
            'cost_price' => 2.50,
        ]);

        $this->actingAs($admin)
            ->post(route('business-admin.purchase-orders.store'), [
                'supplier_id' => $supplier->id,
                'lines' => [
                    ['description' => 'Test Item restock', 'quantity' => 10, 'unit_cost' => 2.50, 'inventory_item_id' => $item->id],
                ],
            ])
            ->assertRedirect();

        $po = PurchaseOrder::query()->latest()->first();
        $this->assertNotNull($po);
        $line = $po->lines->first();

        $this->actingAs($admin)->post(route('business-admin.purchase-orders.submit', $po))->assertRedirect();
        $this->actingAs($admin)->post(route('business-admin.purchase-orders.approve', $po))->assertRedirect();
        $this->actingAs($admin)->post(route('business-admin.purchase-orders.order', $po))->assertRedirect();

        $this->actingAs($admin)->post(route('business-admin.purchase-orders.receive', $po), [
            'lines' => [
                ['purchase_order_line_id' => $line->id, 'quantity_received' => 10, 'quantity_damaged' => 0, 'quantity_missing' => 0],
            ],
        ])->assertRedirect();

        $po->refresh();
        $item->refresh();

        $this->assertSame(PurchaseOrderStatus::Received, $po->status);
        $this->assertSame(15, $item->stock_total_pcs);
        $this->assertNotNull($po->supplier_invoice_id);

        $invoice = $po->supplierInvoice;
        $this->assertSame(SupplierInvoiceStatus::Outstanding, $invoice->status);
        $this->assertDatabaseHas('bills', ['purchase_order_id' => $po->id]);
    }

    public function test_purchase_orders_index_loads_for_admin(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'ava@harbourkitchen.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('business-admin.purchase-orders.index'))
            ->assertOk()
            ->assertSee('Purchase Orders');
    }
}
