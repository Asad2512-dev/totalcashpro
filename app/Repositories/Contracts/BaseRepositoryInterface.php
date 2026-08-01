<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base repository contract for Eloquent-backed persistence.
 *
 * All domain repositories should extend this interface to keep
 * data access consistent and easily swappable for testing.
 */
interface BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $columns
     */
    public function all(array $columns = ['*']): Collection;

    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Model;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int|string $id, array $attributes): Model;

    public function delete(int|string $id): bool;

    /**
     * @param  array<string, mixed>  $columns
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
}
