<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PlanRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection<int, \App\Models\Plan>
     */
    public function orderedActive(): Collection;
}
