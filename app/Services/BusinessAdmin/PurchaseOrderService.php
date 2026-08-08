<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\PurchaseOrderStatus;
use App\Models\GoodsReceivedNote;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderAmendment;
use App\Models\PurchaseOrderLine;
use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Support\Finance\VatCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class PurchaseOrderService implements ServiceInterface
{
    public function __construct(
        private readonly PurchaseOrderRepositoryInterface $purchaseOrders,
        private readonly BranchContext $branchContext,
        private readonly GoodsReceivingService $receiving,
    ) {}

    public function list(User $user, ?string $status = null): mixed
    {
        $filters = $status ? ['status' => $status] : [];

        return $this->purchaseOrders->paginateForBranch(
            (int) $user->organization_id,
            $this->branchContext->currentBranchId($user),
            $filters,
        );
    }

    public function find(User $user, int $id): PurchaseOrder
    {
        $po = $this->purchaseOrders->findForOrganization((int) $user->organization_id, $id);
        $this->authorize($user, $po);

        return $po;
    }

    /**
     * @return array<string, mixed>
     */
    public function formMeta(User $user): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        return [
            'suppliers' => Supplier::query()->where('organization_id', $orgId)->orderBy('name')->get(),
            'items' => InventoryItem::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'cost_price', 'stock_total_pcs', 'stock_limit']),
            'lowStock' => app(InventoryService::class)->list($user)['lowStock'] ?? collect(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>  $lines
     */
    public function create(User $user, array $data, array $lines): PurchaseOrder
    {
        $branchId = $this->branchContext->requireBranchId($user);

        return DB::transaction(function () use ($user, $data, $lines, $branchId): PurchaseOrder {
            $totals = $this->calculateLineTotals($lines);

            $po = PurchaseOrder::query()->create([
                'organization_id' => $user->organization_id,
                'branch_id' => $branchId,
                'supplier_id' => $data['supplier_id'],
                'po_number' => $this->purchaseOrders->nextPoNumber((int) $user->organization_id),
                'status' => PurchaseOrderStatus::Draft,
                'expected_at' => $data['expected_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat'],
                'total' => $totals['total'],
                'created_by' => $user->id,
            ]);

            $this->syncLines($po, $lines);

            return $po->load('lines', 'supplier');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>  $lines
     */
    public function update(User $user, PurchaseOrder $po, array $data, array $lines): PurchaseOrder
    {
        $this->authorize($user, $po);

        if (! in_array($po->status, [PurchaseOrderStatus::Draft, PurchaseOrderStatus::Pending], true)) {
            throw ValidationException::withMessages(['po' => 'Only draft or pending purchase orders can be edited.']);
        }

        return DB::transaction(function () use ($po, $data, $lines): PurchaseOrder {
            $totals = $this->calculateLineTotals($lines);

            $po->update([
                'supplier_id' => $data['supplier_id'],
                'expected_at' => $data['expected_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat'],
                'total' => $totals['total'],
            ]);

            $po->lines()->delete();
            $this->syncLines($po, $lines);

            return $po->fresh(['lines', 'supplier']);
        });
    }

    public function submit(User $user, PurchaseOrder $po): PurchaseOrder
    {
        $this->authorize($user, $po);
        $this->assertStatus($po, [PurchaseOrderStatus::Draft]);

        $po->update(['status' => PurchaseOrderStatus::Pending]);

        return $po;
    }

    public function approve(User $user, PurchaseOrder $po): PurchaseOrder
    {
        $this->authorize($user, $po);
        $this->assertStatus($po, [PurchaseOrderStatus::Pending]);

        $po->update([
            'status' => PurchaseOrderStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        return $po;
    }

    public function markOrdered(User $user, PurchaseOrder $po): PurchaseOrder
    {
        $this->authorize($user, $po);
        $this->assertStatus($po, [PurchaseOrderStatus::Approved]);

        $po->update([
            'status' => PurchaseOrderStatus::Ordered,
            'ordered_at' => now()->toDateString(),
        ]);

        return $po;
    }

    public function cancel(User $user, PurchaseOrder $po): PurchaseOrder
    {
        $this->authorize($user, $po);

        if (in_array($po->status, [PurchaseOrderStatus::Received, PurchaseOrderStatus::Cancelled], true)) {
            throw ValidationException::withMessages(['po' => 'This purchase order cannot be cancelled.']);
        }

        $po->update(['status' => PurchaseOrderStatus::Cancelled]);

        return $po;
    }

    public function markSent(User $user, PurchaseOrder $po): PurchaseOrder
    {
        $this->authorize($user, $po);
        $this->assertStatus($po, [PurchaseOrderStatus::Approved, PurchaseOrderStatus::Ordered]);

        $po->update([
            'status' => PurchaseOrderStatus::Ordered,
            'sent_at' => now(),
            'sent_by' => $user->id,
            'ordered_at' => $po->ordered_at ?? now()->toDateString(),
        ]);

        return $po;
    }

    /**
     * @param  list<array{purchase_order_line_id: int, quantity_received: float, quantity_damaged?: float, quantity_missing?: float, accept_over_delivery?: bool}>  $lines
     */
    public function receiveGoods(User $user, PurchaseOrder $po, array $lines, ?string $notes = null, bool $allowOverDelivery = false): GoodsReceivedNote
    {
        $this->authorize($user, $po);

        return $this->receiving->receive(
            $user,
            $po,
            $lines,
            $notes,
            $po->delivery?->id,
            $allowOverDelivery,
        );
    }

    public function amend(User $user, PurchaseOrder $po, string $field, mixed $newValue, string $reason): PurchaseOrder
    {
        $this->authorize($user, $po);

        if (! in_array($po->status, [PurchaseOrderStatus::Approved, PurchaseOrderStatus::Ordered, PurchaseOrderStatus::PartiallyReceived], true)) {
            throw ValidationException::withMessages(['po' => 'Only approved or in-progress purchase orders can be amended.']);
        }

        $oldValue = $po->getAttribute($field);

        PurchaseOrderAmendment::query()->create([
            'purchase_order_id' => $po->id,
            'amended_by' => $user->id,
            'field' => $field,
            'old_value' => is_scalar($oldValue) || $oldValue === null ? (string) $oldValue : json_encode($oldValue),
            'new_value' => is_scalar($newValue) || $newValue === null ? (string) $newValue : json_encode($newValue),
            'reason' => $reason,
        ]);

        $po->update([$field => $newValue]);

        return $po->refresh();
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     */
    private function syncLines(PurchaseOrder $po, array $lines): void
    {
        foreach ($lines as $line) {
            if (empty($line['description']) && empty($line['inventory_item_id'])) {
                continue;
            }

            $qty = (float) ($line['quantity'] ?? 0);
            $unitCost = (float) ($line['unit_cost'] ?? 0);
            $vatRate = (float) ($line['vat_rate'] ?? 20);
            $lineNet = round($qty * $unitCost, 2);

            PurchaseOrderLine::query()->create([
                'purchase_order_id' => $po->id,
                'inventory_item_id' => $line['inventory_item_id'] ?? null,
                'description' => $line['description'] ?? 'Line item',
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'snapshot_unit_cost' => $unitCost,
                'vat_rate' => $vatRate,
                'line_total' => $lineNet,
            ]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     * @return array{subtotal: float, vat: float, total: float}
     */
    private function calculateLineTotals(array $lines): array
    {
        $subtotal = 0.0;
        $vat = 0.0;

        foreach ($lines as $line) {
            $qty = (float) ($line['quantity'] ?? 0);
            $unitCost = (float) ($line['unit_cost'] ?? 0);
            $vatRate = (float) ($line['vat_rate'] ?? 20);
            $net = round($qty * $unitCost, 2);
            $amounts = VatCalculator::fromNet($net, $vatRate);
            $subtotal += $amounts['net'];
            $vat += $amounts['vat'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'vat' => round($vat, 2),
            'total' => round($subtotal + $vat, 2),
        ];
    }

    /**
     * @param  list<PurchaseOrderStatus>  $allowed
     */
    private function assertStatus(PurchaseOrder $po, array $allowed): void
    {
        if (! in_array($po->status, $allowed, true)) {
            throw ValidationException::withMessages(['po' => 'Invalid purchase order status for this action.']);
        }
    }

    private function authorize(User $user, PurchaseOrder $po): void
    {
        if ((int) $po->organization_id !== (int) $user->organization_id) {
            abort(403);
        }
    }
}
