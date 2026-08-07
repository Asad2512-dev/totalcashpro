<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\FinanceBankAccount;
use Illuminate\Database\Eloquent\Collection;

interface FinanceBankAccountRepositoryInterface extends BaseRepositoryInterface
{
    public function forOrganization(int $organizationId, ?int $branchId = null): Collection;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAccount(array $data): FinanceBankAccount;
}
