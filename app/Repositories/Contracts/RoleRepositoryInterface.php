<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Role;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Role;
}
