<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FinanceBankAccount;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class FinanceBankAccountRepository extends BaseRepository implements FinanceBankAccountRepositoryInterface
{
    public function __construct(FinanceBankAccount $model)
    {
        parent::__construct($model);
    }

    public function forOrganization(int $organizationId, ?int $branchId = null): Collection
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where(function ($inner) use ($branchId): void {
                $inner->whereNull('branch_id')->orWhere('branch_id', $branchId);
            }))
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    public function createAccount(array $data): FinanceBankAccount
    {
        return $this->model->newQuery()->create($data);
    }
}
