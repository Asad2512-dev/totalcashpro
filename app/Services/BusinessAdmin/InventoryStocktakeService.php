<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\InventoryStocktakeStatus;
use App\Enums\InventoryTransactionType;
use App\Enums\PurchaseOrderStatus;
use App\Models\InventoryItem;
use App\Models\InventoryStocktake;
use App\Models\InventoryStocktakeItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class InventoryStocktakeService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly InventoryReplenishmentService $replenishment,
        private readonly InventoryLedgerService $ledger,
        private readonly VendorOrderService $vendorOrders,
    ) {}

    public function currentOrCreate(User $user, ?Carbon $weekStart = null): InventoryStocktake
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $weekStart ??= now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $existing = InventoryStocktake::query()
            ->where('organization_id', $user->organization_id)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $weekStart->toDateString())
            ->whereNotIn('status', [
                InventoryStocktakeStatus::Archived->value,
                InventoryStocktakeStatus::Completed->value,
            ])
            ->first();

        if ($existing !== null) {
            return $existing->load(['items.item.category', 'items.supplier']);
        }

        return DB::transaction(function () use ($user, $branchId, $weekStart, $weekEnd): InventoryStocktake {
            $stocktake = InventoryStocktake::query()->create([
                'organization_id' => $user->organization_id,
                'branch_id' => $branchId,
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'status' => InventoryStocktakeStatus::InProgress->value,
                'created_by' => $user->id,
                'source' => 'web',
            ]);

            $items = InventoryItem::query()
                ->with('category')
                ->where('organization_id', $user->organization_id)
                ->where('branch_id', $branchId)
                ->orderBy('name')
                ->get();

            foreach ($items as $item) {
                $par = $this->replenishment->parLevelForItem($item);
                $onOrder = $this->replenishment->onOrderQuantity($item);

                InventoryStocktakeItem::query()->create([
                    'inventory_stocktake_id' => $stocktake->id,
                    'inventory_item_id' => $item->id,
                    'system_qty' => $item->stock_total_pcs,
                    'par_level' => $par,
                    'on_order_qty' => $onOrder,
                    'supplier_id' => $item->supplier_id,
                ]);
            }

            return $stocktake->load(['items.item.category', 'items.supplier']);
        });
    }

    /**
     * @param  list<array{inventory_item_id: int, counted_qty: float, notes?: string}>  $rows
     */
    public function saveCounts(User $user, InventoryStocktake $stocktake, array $rows): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);

        if (! $stocktake->isEditableByStaff()) {
            throw ValidationException::withMessages(['stocktake' => 'This stocktake cannot be edited.']);
        }

        foreach ($rows as $row) {
            $line = $stocktake->items()->where('inventory_item_id', (int) $row['inventory_item_id'])->first();
            if ($line === null) {
                continue;
            }

            $item = $line->item ?? InventoryItem::query()->find($line->inventory_item_id);
            $counted = (float) $row['counted_qty'];
            $system = (float) $line->system_qty;
            $par = (float) $line->par_level;
            $onOrder = (float) $line->on_order_qty;

            $suggested = $this->replenishment->suggestedOrder(
                $par,
                $counted,
                $onOrder,
                (int) ($item?->order_multiple ?? 1),
                (int) ($item?->pack_size ?? 1),
            );

            $line->update([
                'counted_qty' => $counted,
                'difference_qty' => round($counted - $system, 3),
                'suggested_order_qty' => $suggested,
                'notes' => $row['notes'] ?? $line->notes,
            ]);
        }

        return $stocktake->refresh()->load(['items.item.category', 'items.supplier']);
    }

    public function submit(User $user, InventoryStocktake $stocktake): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);

        if (! $stocktake->isEditableByStaff()) {
            throw ValidationException::withMessages(['stocktake' => 'This stocktake cannot be submitted.']);
        }

        $incomplete = $stocktake->items()->whereNull('counted_qty')->count();
        if ($incomplete > 0) {
            throw ValidationException::withMessages([
                'stocktake' => "{$incomplete} items still need a physical count.",
            ]);
        }

        $stocktake->update([
            'status' => InventoryStocktakeStatus::Submitted->value,
            'submitted_at' => now(),
        ]);

        return $stocktake->refresh();
    }

    public function startReview(User $user, InventoryStocktake $stocktake): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);
        $stocktake->update([
            'status' => InventoryStocktakeStatus::UnderReview->value,
            'reviewed_by' => $user->id,
        ]);

        return $stocktake->refresh();
    }

    /**
     * @param  list<array{inventory_stocktake_item_id: int, ordered_qty?: float, excluded_from_order?: bool, override_reason?: string, supplier_id?: int}>  $adjustments
     */
    public function approve(User $user, InventoryStocktake $stocktake, array $adjustments = []): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);

        return DB::transaction(function () use ($user, $stocktake, $adjustments): InventoryStocktake {
            foreach ($adjustments as $adj) {
                $line = InventoryStocktakeItem::query()
                    ->where('inventory_stocktake_id', $stocktake->id)
                    ->find((int) $adj['inventory_stocktake_item_id']);

                if ($line === null) {
                    continue;
                }

                $ordered = isset($adj['ordered_qty']) ? (float) $adj['ordered_qty'] : (float) $line->suggested_order_qty;
                $reason = $adj['override_reason'] ?? null;

                if (abs($ordered - (float) $line->suggested_order_qty) > 0.009 && empty($reason)) {
                    throw ValidationException::withMessages([
                        'override_reason' => 'Provide a reason when changing suggested order quantities.',
                    ]);
                }

                $line->update([
                    'ordered_qty' => $ordered,
                    'excluded_from_order' => (bool) ($adj['excluded_from_order'] ?? false),
                    'override_reason' => $reason,
                    'supplier_id' => $adj['supplier_id'] ?? $line->supplier_id,
                ]);
            }

            foreach ($stocktake->items()->with('item')->get() as $line) {
                if ($line->counted_qty === null || $line->item === null) {
                    continue;
                }

                $diff = (float) $line->difference_qty;
                if (abs($diff) < 0.001) {
                    continue;
                }

                $this->ledger->record(
                    $user,
                    $line->item,
                    InventoryTransactionType::Stocktake,
                    $diff,
                    InventoryStocktake::class,
                    $stocktake->id,
                    'Weekly stocktake adjustment',
                );
            }

            $stocktake->update([
                'status' => InventoryStocktakeStatus::Approved->value,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $this->vendorOrders->generateFromStocktake($user, $stocktake->refresh());

            return $stocktake->refresh();
        });
    }

    public function reject(User $user, InventoryStocktake $stocktake, ?string $reason = null): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);
        $stocktake->update([
            'status' => InventoryStocktakeStatus::Rejected->value,
            'notes' => $reason ?? $stocktake->notes,
        ]);

        return $stocktake->refresh();
    }

    public function reopen(User $user, InventoryStocktake $stocktake): InventoryStocktake
    {
        $this->assertAccess($user, $stocktake);
        $stocktake->update(['status' => InventoryStocktakeStatus::InProgress->value]);

        return $stocktake->refresh();
    }

    public function listForAdmin(User $user, ?string $status = null): mixed
    {
        $branchId = $this->branchContext->currentBranchId($user);

        return InventoryStocktake::query()
            ->with(['branch', 'creator', 'items'])
            ->where('organization_id', $user->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('week_start')
            ->paginate(20);
    }

    private function assertAccess(User $user, InventoryStocktake $stocktake): void
    {
        if ((int) $stocktake->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        if ($user->isStaff() && (int) $stocktake->branch_id !== (int) $user->branch_id) {
            abort(403);
        }
    }
}
