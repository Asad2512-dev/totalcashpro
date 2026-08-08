<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierStatus;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierPriceHistory;
use App\Models\SupplierProduct;
use App\Models\User;

final class SupplierProductService implements ServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, Supplier $supplier, array $data): SupplierProduct
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        $unitCost = (float) ($data['unit_cost'] ?? 0);

        $product = SupplierProduct::query()->create([
            'organization_id' => $user->organization_id,
            'supplier_id' => $supplier->id,
            'inventory_item_id' => $data['inventory_item_id'],
            'supplier_sku' => $data['supplier_sku'] ?? null,
            'pack_size' => $data['pack_size'] ?? 1,
            'unit' => $data['unit'] ?? 'pcs',
            'unit_cost' => $unitCost,
            'vat_rate' => $data['vat_rate'] ?? 20,
            'moq' => $data['moq'] ?? 0,
            'order_multiple' => $data['order_multiple'] ?? 1,
            'lead_time_days' => $data['lead_time_days'] ?? 0,
            'is_primary' => (bool) ($data['is_primary'] ?? false),
            'active' => true,
            'effective_from' => $data['effective_from'] ?? now()->toDateString(),
        ]);

        SupplierPriceHistory::query()->create([
            'organization_id' => $user->organization_id,
            'supplier_id' => $supplier->id,
            'inventory_item_id' => $data['inventory_item_id'],
            'unit_cost' => $unitCost,
            'unit' => $data['unit'] ?? 'pcs',
            'effective_from' => $product->effective_from ?? now()->toDateString(),
            'created_by' => $user->id,
        ]);

        return $product;
    }

    public function recordPriceChange(User $user, SupplierProduct $product, float $newCost): SupplierPriceHistory
    {
        abort_unless((int) $product->organization_id === (int) $user->organization_id, 403);

        SupplierPriceHistory::query()
            ->where('supplier_id', $product->supplier_id)
            ->where('inventory_item_id', $product->inventory_item_id)
            ->whereNull('effective_until')
            ->update(['effective_until' => now()->toDateString()]);

        $product->update(['unit_cost' => $newCost]);

        return SupplierPriceHistory::query()->create([
            'organization_id' => $user->organization_id,
            'supplier_id' => $product->supplier_id,
            'inventory_item_id' => $product->inventory_item_id,
            'unit_cost' => $newCost,
            'unit' => $product->unit ?? 'pcs',
            'effective_from' => now()->toDateString(),
            'created_by' => $user->id,
        ]);
    }
}
