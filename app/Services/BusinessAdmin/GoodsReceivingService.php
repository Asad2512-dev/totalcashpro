<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\InventoryTransactionType;
use App\Enums\PurchaseOrderStatus;
use App\Events\PurchaseOrderReceived;
use App\Models\Delivery;
use App\Models\GoodsReceivedLine;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReturn;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\User;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class GoodsReceivingService implements ServiceInterface
{
    public function __construct(
        private readonly InventoryLedgerService $ledger,
        private readonly PurchaseOrderRepositoryInterface $purchaseOrders,
    ) {}

    public function nextGrnNumber(int $organizationId): string
    {
        $count = GoodsReceivedNote::query()
            ->where('organization_id', $organizationId)
            ->count() + 1;

        return sprintf('GRN-%05d', $count);
    }

    /**
     * @param  list<array{purchase_order_line_id: int, quantity_received: float, quantity_damaged?: float, quantity_missing?: float, accept_over_delivery?: bool}>  $lines
     */
    public function receive(
        User $user,
        PurchaseOrder $po,
        array $lines,
        ?string $notes = null,
        ?int $deliveryId = null,
        bool $allowOverDelivery = false,
    ): GoodsReceivedNote {
        if ((int) $po->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        if (! in_array($po->status, [PurchaseOrderStatus::Ordered, PurchaseOrderStatus::PartiallyReceived, PurchaseOrderStatus::Received], true)) {
            if (! in_array($po->status, [PurchaseOrderStatus::Approved], true)) {
                throw ValidationException::withMessages(['po' => 'Goods can only be received for approved or ordered purchase orders.']);
            }
        }

        return DB::transaction(function () use ($user, $po, $lines, $notes, $deliveryId, $allowOverDelivery): GoodsReceivedNote {
            $grn = GoodsReceivedNote::query()->create([
                'purchase_order_id' => $po->id,
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'delivery_id' => $deliveryId,
                'grn_number' => $this->nextGrnNumber((int) $po->organization_id),
                'received_at' => now()->toDateString(),
                'received_by' => $user->id,
                'notes' => $notes,
                'status' => 'completed',
            ]);

            foreach ($lines as $row) {
                $poLine = PurchaseOrderLine::query()
                    ->where('purchase_order_id', $po->id)
                    ->findOrFail((int) $row['purchase_order_line_id']);

                $received = (float) ($row['quantity_received'] ?? 0);
                $damaged = (float) ($row['quantity_damaged'] ?? 0);
                $missing = (float) ($row['quantity_missing'] ?? 0);
                $accepted = max(0, $received - $damaged);

                $outstanding = max(0, (float) $poLine->quantity - (float) $poLine->quantity_received);
                $overQty = max(0, $received - $outstanding);

                if ($overQty > 0 && ! $allowOverDelivery && ! ($row['accept_over_delivery'] ?? false)) {
                    throw ValidationException::withMessages([
                        'lines' => sprintf('Received quantity exceeds outstanding by %s for %s. Admin approval required.', $overQty, $poLine->description),
                    ]);
                }

                GoodsReceivedLine::query()->create([
                    'goods_received_note_id' => $grn->id,
                    'purchase_order_line_id' => $poLine->id,
                    'quantity_received' => $received,
                    'quantity_damaged' => $damaged,
                    'quantity_missing' => $missing,
                    'quantity_accepted' => $accepted,
                ]);

                $poLine->update([
                    'quantity_received' => (float) $poLine->quantity_received + $received,
                ]);

                if ($poLine->inventory_item_id && $accepted > 0) {
                    $item = InventoryItem::query()->findOrFail($poLine->inventory_item_id);
                    $this->ledger->record(
                        $user,
                        $item,
                        InventoryTransactionType::Receipt,
                        $accepted,
                        GoodsReceivedNote::class,
                        $grn->id,
                        $notes,
                    );
                }
            }

            $po->refresh()->load('lines');
            $po->update([
                'status' => $po->isFullyReceived()
                    ? PurchaseOrderStatus::Received
                    : PurchaseOrderStatus::PartiallyReceived,
            ]);

            if ($deliveryId) {
                Delivery::query()->whereKey($deliveryId)->update(['awaiting_receiving' => false]);
            }

            PurchaseOrderReceived::dispatch($po, $grn, $user);

            return $grn->load('lines.purchaseOrderLine');
        });
    }

  /**
   * @param  array<string, mixed>  $data
   */
    public function recordReturn(User $user, array $data): GoodsReturn
    {
        return GoodsReturn::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $data['branch_id'],
            'supplier_id' => $data['supplier_id'],
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'goods_received_note_id' => $data['goods_received_note_id'] ?? null,
            'inventory_item_id' => $data['inventory_item_id'] ?? null,
            'quantity' => $data['quantity'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);
    }
}
