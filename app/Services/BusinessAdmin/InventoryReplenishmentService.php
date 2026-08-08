<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\PurchaseOrderStatus;
use App\Models\InventoryItem;
use App\Models\PurchaseOrderLine;

final class InventoryReplenishmentService implements ServiceInterface
{
    public function onOrderQuantity(InventoryItem $item): float
    {
        return (float) PurchaseOrderLine::query()
            ->whereHas('purchaseOrder', function ($q) use ($item): void {
                $q->where('organization_id', $item->organization_id)
                    ->where('branch_id', $item->branch_id)
                    ->whereIn('status', [
                        PurchaseOrderStatus::Approved->value,
                        PurchaseOrderStatus::Ordered->value,
                        PurchaseOrderStatus::PartiallyReceived->value,
                    ]);
            })
            ->where('inventory_item_id', $item->id)
            ->selectRaw('COALESCE(SUM(quantity - quantity_received), 0) as remaining')
            ->value('remaining');
    }

    public function suggestedOrder(
        float $parLevel,
        float $currentStock,
        float $onOrder = 0.0,
        int $orderMultiple = 1,
        int $packSize = 1,
        float $moq = 0.0,
    ): float {
        $needed = max(0, $parLevel - $currentStock - $onOrder);

        if ($needed <= 0) {
            return 0.0;
        }

        if ($orderMultiple > 1) {
            $needed = (float) (ceil($needed / $orderMultiple) * $orderMultiple);
        }

        if ($packSize > 1) {
            $needed = (float) (ceil($needed / $packSize) * $packSize);
        }

        if ($moq > 0 && $needed < $moq) {
            $needed = $moq;
        }

        return round($needed, 3);
    }

    public function parLevelForItem(InventoryItem $item): float
    {
        return (float) ($item->par_level > 0 ? $item->par_level : $item->stock_limit);
    }
}
