<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\InventoryStocktakeStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\InventoryStocktake;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\User;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class VendorOrderService implements ServiceInterface
{
    public function __construct(
        private readonly PurchaseOrderRepositoryInterface $purchaseOrders,
    ) {}

    /**
     * @return Collection<int, PurchaseOrder>
     */
    public function generateFromStocktake(User $user, InventoryStocktake $stocktake): Collection
    {
        $stocktake->load(['items.item', 'items.supplier']);

        $grouped = $stocktake->items
            ->filter(fn ($line) => ! $line->excluded_from_order && $line->effectiveOrderQty() > 0)
            ->groupBy(fn ($line) => $line->supplier_id ?? $line->item?->supplier_id ?? 0);

        $created = collect();

        foreach ($grouped as $supplierId => $lines) {
            if ((int) $supplierId <= 0) {
                continue;
            }

            $supplier = Supplier::query()
                ->where('organization_id', $user->organization_id)
                ->find((int) $supplierId);

            if ($supplier === null) {
                continue;
            }

            $po = DB::transaction(function () use ($user, $stocktake, $supplier, $lines): PurchaseOrder {
                $subtotal = 0.0;

                $po = PurchaseOrder::query()->create([
                    'organization_id' => $user->organization_id,
                    'branch_id' => $stocktake->branch_id,
                    'supplier_id' => $supplier->id,
                    'po_number' => $this->purchaseOrders->nextPoNumber((int) $user->organization_id),
                    'status' => PurchaseOrderStatus::Draft,
                    'notes' => sprintf('From stocktake week %s', $stocktake->week_start?->format('d M Y')),
                    'subtotal' => 0,
                    'vat_total' => 0,
                    'total' => 0,
                    'created_by' => $user->id,
                ]);

                foreach ($lines as $line) {
                    $item = $line->item;
                    if ($item === null) {
                        continue;
                    }

                    $qty = $line->effectiveOrderQty();
                    $mapping = SupplierProduct::query()
                        ->where('supplier_id', $supplier->id)
                        ->where('inventory_item_id', $item->id)
                        ->first();

                    $unitCost = (float) ($mapping?->unit_cost ?? $item->cost_price ?? 0);
                    $lineNet = round($qty * $unitCost, 2);
                    $subtotal += $lineNet;

                    $po->lines()->create([
                        'inventory_item_id' => $item->id,
                        'supplier_id' => $supplier->id,
                        'description' => $item->name,
                        'quantity' => $qty,
                        'unit_cost' => $unitCost,
                        'snapshot_unit_cost' => $unitCost,
                        'snapshot_pack_size' => $mapping?->pack_size ?? $item->pack_size,
                        'snapshot_supplier_sku' => $mapping?->supplier_sku,
                        'vat_rate' => 20,
                        'line_total' => $lineNet,
                    ]);
                }

                $vat = round($subtotal * 0.2, 2);
                $po->update([
                    'subtotal' => round($subtotal, 2),
                    'vat_total' => $vat,
                    'total' => round($subtotal + $vat, 2),
                ]);

                return $po->load('lines', 'supplier');
            });

            $created->push($po);
        }

        $stocktake->update(['status' => InventoryStocktakeStatus::Ordered->value]);

        return $created;
    }
}
