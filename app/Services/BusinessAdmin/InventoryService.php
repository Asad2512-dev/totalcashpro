<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\InventoryCategory;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Models\User;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class InventoryService implements ServiceInterface
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventory,
        private readonly BranchContext $branchContext,
    ) {}

    public function list(User $user, ?string $search = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        return [
            'items' => $this->inventory->paginateItems($orgId, $branchId, $search),
            'categories' => InventoryCategory::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->orderBy('name')
                ->get(),
            'lowStock' => $this->inventory->lowStock($orgId, $branchId),
            'branches' => $this->branchContext->resolveBranches($user),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeCategory(User $user, array $data): InventoryCategory
    {
        $branchId = (int) ($data['branch_id'] ?? $this->branchContext->requireBranchId($user));
        $this->branchContext->ensureBranchBelongsToOrg($branchId, $user);

        return InventoryCategory::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeItem(User $user, array $data): InventoryItem
    {
        $branchId = (int) ($data['branch_id'] ?? $this->branchContext->requireBranchId($user));
        $this->branchContext->ensureBranchBelongsToOrg($branchId, $user);

        return InventoryItem::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'packaging' => $data['packaging'] ?? 'pcs',
            'pcs_per_box' => (int) ($data['pcs_per_box'] ?? 1),
            'stock_total_pcs' => (int) ($data['stock_total_pcs'] ?? 0),
            'stock_limit' => (int) ($data['stock_limit'] ?? 0),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function recordCount(User $user, array $data): InventoryCount
    {
        $item = InventoryItem::query()->findOrFail((int) $data['item_id']);

        if ((int) $item->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $newPcs = (int) $data['new_pcs'];

        return $this->inventory->recordCount([
            'organization_id' => $user->organization_id,
            'branch_id' => $item->branch_id,
            'item_id' => $item->id,
            'diff_pcs' => $newPcs - (int) $item->stock_total_pcs,
            'new_pcs' => $newPcs,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);
    }

    /**
     * @return array{counts: LengthAwarePaginator|Collection, from: Carbon, to: Carbon}
     */
    public function history(User $user, ?string $from = null, ?string $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $fromDate = Carbon::parse($from ?: now()->subDays(30)->toDateString())->startOfDay();
        $toDate = Carbon::parse($to ?: now()->toDateString())->endOfDay();

        $counts = InventoryCount::query()
            ->with(['item.category', 'creator', 'branch'])
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return [
            'counts' => $counts,
            'from' => $fromDate,
            'to' => $toDate,
        ];
    }
}
