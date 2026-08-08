<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\InventoryTransactionType;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class InventoryLedgerService implements ServiceInterface
{
    public function record(
        User $user,
        InventoryItem $item,
        InventoryTransactionType $type,
        float $quantityDelta,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $reason = null,
        bool $updateStock = true,
    ): InventoryTransaction {
        if ((int) $item->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return DB::transaction(function () use ($user, $item, $type, $quantityDelta, $referenceType, $referenceId, $reason, $updateStock): InventoryTransaction {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($item->id);
            $before = (float) $item->stock_total_pcs;
            $after = $before + $quantityDelta;

            if ($updateStock) {
                $item->update(['stock_total_pcs' => (int) round(max(0, $after))]);
            }

            return InventoryTransaction::query()->create([
                'organization_id' => $item->organization_id,
                'branch_id' => $item->branch_id,
                'inventory_item_id' => $item->id,
                'type' => $type->value,
                'quantity' => $quantityDelta,
                'quantity_before' => $before,
                'quantity_after' => max(0, $after),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => $reason,
                'created_by' => $user->id,
            ]);
        });
    }
}
