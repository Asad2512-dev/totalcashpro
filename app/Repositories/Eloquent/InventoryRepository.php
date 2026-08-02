<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\InventoryCategory;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    public function __construct(InventoryItem $model)
    {
        parent::__construct($model);
    }

    public function getCategoriesForBranch(int $organizationId, int $branchId): Collection
    {
        return InventoryCategory::query()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();
    }

    public function getItemsForBranch(int $organizationId, int $branchId, ?int $categoryId = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId);

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        return $query->with('category')->orderBy('name')->get();
    }

    public function getLowStockItems(int $organizationId, ?int $branchId = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereRaw('stock_total_pcs <= stock_limit');

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->with(['category', 'branch'])->orderBy('stock_total_pcs')->get();
    }

    public function createCategory(array $data): InventoryCategory
    {
        return InventoryCategory::query()->create($data);
    }

    public function createItem(array $data): InventoryItem
    {
        return $this->model->newQuery()->create($data);
    }

    public function recordCount(array $data): InventoryCount
    {
        $count = InventoryCount::query()->create($data);

        // Update the item's stock total
        if (isset($data['item_id']) && isset($data['new_pcs'])) {
            $this->updateItemStock($data['item_id'], $data['new_pcs']);
        }

        return $count;
    }

    public function updateItemStock(int $itemId, int $newStockPcs): InventoryItem
    {
        $item = $this->model->newQuery()->findOrFail($itemId);
        $item->update(['stock_total_pcs' => $newStockPcs]);

        return $item->refresh();
    }

    public function lowStock(int $organizationId, ?int $branchId): Collection
    {
        return $this->getLowStockItems($organizationId, $branchId);
    }

    public function paginateItems(int $organizationId, ?int $branchId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['category', 'branch'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function recentCounts(int $organizationId, ?int $branchId, int $limit = 50): Collection
    {
        return InventoryCount::query()
            ->with(['item', 'creator', 'branch'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->limit($limit)
            ->get();
    }
}
