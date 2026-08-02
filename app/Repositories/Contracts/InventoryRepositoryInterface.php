<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\InventoryCategory;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Collection;

interface InventoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all categories for a branch.
     */
    public function getCategoriesForBranch(int $organizationId, int $branchId): Collection;

    /**
     * Get all items for a branch.
     */
    public function getItemsForBranch(int $organizationId, int $branchId, ?int $categoryId = null): Collection;

    /**
     * Get low stock items.
     */
    public function getLowStockItems(int $organizationId, ?int $branchId = null): Collection;

    /**
     * Create a category.
     *
     * @param  array<string, mixed>  $data
     */
    public function createCategory(array $data): InventoryCategory;

    /**
     * Create an item.
     *
     * @param  array<string, mixed>  $data
     */
    public function createItem(array $data): InventoryItem;

    /**
     * Record a stock count adjustment.
     *
     * @param  array<string, mixed>  $data
     */
    public function recordCount(array $data): InventoryCount;

    /**
     * Update item stock.
     */
    public function updateItemStock(int $itemId, int $newStockPcs): InventoryItem;

    public function lowStock(int $organizationId, ?int $branchId): Collection;

    public function paginateItems(int $organizationId, ?int $branchId, ?string $search = null, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * @return Collection<int, InventoryCount>
     */
    public function recentCounts(int $organizationId, ?int $branchId, int $limit = 50): Collection;
}
