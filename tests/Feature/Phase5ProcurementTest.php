<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DeliveryStatus;
use App\Enums\InvoiceMatchStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\Delivery;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\BusinessAdmin\GoodsReceivingService;
use App\Services\BusinessAdmin\InvoiceMatchingService;
use App\Services\BusinessAdmin\RiderDeliveryService;
use App\Services\BusinessAdmin\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Phase5ProcurementTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_critical_1_three_way_match_passes_with_grn_accepted_quantity(): void
    {
        [$admin, $po, $line, $item] = $this->makeOrderedPo(100, 5);

        $grn = app(GoodsReceivingService::class)->receive($admin, $po, [[
            'purchase_order_line_id' => $line->id,
            'quantity_received' => 95,
            'quantity_damaged' => 0,
            'quantity_missing' => 5,
        ]]);

        $item->refresh();
        $this->assertEquals(95, $item->stock_total_pcs);

        $invoice = app(InvoiceMatchingService::class)->createInvoice($admin, $po->fresh(), [
            'invoice_no' => 'INV-MATCH-1',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Chicken',
            'quantity' => 95,
            'unit_cost' => 5,
            'purchase_order_line_id' => $line->id,
        ]], $grn->id);

        $this->assertEquals(InvoiceMatchStatus::Matched, $invoice->invoiceMatch?->status);
        $this->assertNotEquals(100, $item->stock_total_pcs);
    }

    public function test_critical_2_quantity_mismatch_when_invoice_exceeds_grn(): void
    {
        [$admin, $po, $line] = $this->makeOrderedPo(100, 5);

        $grn = app(GoodsReceivingService::class)->receive($admin, $po, [[
            'purchase_order_line_id' => $line->id,
            'quantity_received' => 90,
            'quantity_damaged' => 0,
            'quantity_missing' => 10,
        ]]);

        $invoice = app(InvoiceMatchingService::class)->createInvoice($admin, $po->fresh(), [
            'invoice_no' => 'INV-MISMATCH-QTY',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Chicken',
            'quantity' => 100,
            'unit_cost' => 5,
            'purchase_order_line_id' => $line->id,
        ]], $grn->id);

        $this->assertEquals(InvoiceMatchStatus::Mismatch, $invoice->invoiceMatch?->status);
        $this->assertEquals(10.0, (float) $invoice->invoiceMatch?->quantity_variance);
    }

    public function test_critical_3_price_mismatch(): void
    {
        [$admin, $po, $line] = $this->makeOrderedPo(100, 5);

        $grn = app(GoodsReceivingService::class)->receive($admin, $po, [[
            'purchase_order_line_id' => $line->id,
            'quantity_received' => 100,
            'quantity_damaged' => 0,
            'quantity_missing' => 0,
        ]]);

        $invoice = app(InvoiceMatchingService::class)->createInvoice($admin, $po->fresh(), [
            'invoice_no' => 'INV-MISMATCH-PRICE',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Chicken',
            'quantity' => 100,
            'unit_cost' => 5.50,
            'purchase_order_line_id' => $line->id,
        ]], $grn->id);

        $this->assertEquals(InvoiceMatchStatus::Mismatch, $invoice->invoiceMatch?->status);
        $this->assertEquals(50.0, (float) $invoice->invoiceMatch?->price_variance);
    }

    public function test_critical_4_duplicate_invoice_per_supplier_blocked(): void
    {
        [$admin, $po, $line] = $this->makeOrderedPo(10, 5);

        app(InvoiceMatchingService::class)->createInvoice($admin, $po, [
            'invoice_no' => '123',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Item',
            'quantity' => 10,
            'unit_cost' => 5,
            'purchase_order_line_id' => $line->id,
        ]]);

        $supplierB = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $po->branch_id,
            'name' => 'Supplier B',
        ]);

        $poB = PurchaseOrder::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $po->branch_id,
            'supplier_id' => $supplierB->id,
            'po_number' => 'PO-B-123',
            'status' => PurchaseOrderStatus::Ordered,
            'subtotal' => 50,
            'vat_total' => 10,
            'total' => 60,
            'created_by' => $admin->id,
        ]);

        $lineB = $poB->lines()->create([
            'description' => 'Bread',
            'quantity' => 10,
            'unit_cost' => 5,
            'line_total' => 50,
        ]);

        app(InvoiceMatchingService::class)->createInvoice($admin, $poB, [
            'invoice_no' => '123',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Bread',
            'quantity' => 10,
            'unit_cost' => 5,
            'purchase_order_line_id' => $lineB->id,
        ]]);

        $this->expectException(ValidationException::class);
        app(InvoiceMatchingService::class)->createInvoice($admin, $po->fresh(), [
            'invoice_no' => '123',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
        ], [[
            'description' => 'Item',
            'quantity' => 10,
            'unit_cost' => 5,
            'purchase_order_line_id' => $line->id,
        ]]);
    }

    public function test_critical_5_delivery_delivered_does_not_update_inventory(): void
    {
        [$admin, $po, $line, $item] = $this->makeOrderedPo(50, 4);
        $stockBefore = $item->stock_total_pcs;

        $riderProfile = app(\App\Services\BusinessAdmin\RiderService::class)->create($admin, [
            'name' => 'Rider',
            'email' => 'rider-p5@example.com',
            'branch_id' => $po->branch_id,
        ]);

        $delivery = app(RiderDeliveryService::class)->assign($admin, $po, $riderProfile);
        app(RiderDeliveryService::class)->accept($riderProfile, $delivery);
        app(RiderDeliveryService::class)->advanceStatus($riderProfile, $delivery->fresh(), DeliveryStatus::Collected);
        $delivered = app(RiderDeliveryService::class)->advanceStatus($riderProfile, $delivery->fresh(), DeliveryStatus::Delivered);

        $item->refresh();
        $this->assertEquals($stockBefore, $item->stock_total_pcs);
        $this->assertTrue($delivered->awaiting_receiving);
    }

    public function test_grn_increases_inventory_by_accepted_only(): void
    {
        [$admin, $po, $line, $item] = $this->makeOrderedPo(100, 5);
        $before = $item->stock_total_pcs;

        app(GoodsReceivingService::class)->receive($admin, $po, [[
            'purchase_order_line_id' => $line->id,
            'quantity_received' => 95,
            'quantity_damaged' => 5,
            'quantity_missing' => 0,
        ]]);

        $item->refresh();
        $this->assertEquals($before + 90, $item->stock_total_pcs);
    }

    public function test_supplier_contact_and_duplicate_invoice_via_service(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $supplier = app(SupplierService::class)->storeSupplier($admin, ['name' => 'Fresh Foods']);
        app(SupplierService::class)->storeContact($admin, $supplier, [
            'name' => 'Accounts',
            'role' => 'accounts',
            'email' => 'accounts@fresh.test',
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('supplier_contacts', ['supplier_id' => $supplier->id, 'name' => 'Accounts']);
    }

    /**
     * @return array{0: \App\Models\User, 1: PurchaseOrder, 2: \App\Models\PurchaseOrderLine, 3: InventoryItem}
     */
    private function makeOrderedPo(float $qty, float $unitCost): array
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        $supplier = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Test Vendor',
        ]);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Chicken',
            'packaging' => 'kg',
            'unit' => 'kg',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 0,
            'cost_price' => $unitCost,
        ]);

        $po = PurchaseOrder::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-P5-'.uniqid(),
            'status' => PurchaseOrderStatus::Ordered,
            'subtotal' => $qty * $unitCost,
            'vat_total' => 0,
            'total' => $qty * $unitCost,
            'created_by' => $admin->id,
        ]);

        $line = $po->lines()->create([
            'inventory_item_id' => $item->id,
            'description' => 'Chicken',
            'quantity' => $qty,
            'unit_cost' => $unitCost,
            'line_total' => $qty * $unitCost,
        ]);

        return [$admin, $po, $line, $item];
    }
}
