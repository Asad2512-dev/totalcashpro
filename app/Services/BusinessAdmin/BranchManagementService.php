<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\FinanceBankAccount;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class BranchManagementService implements ServiceInterface
{
    /**
     * @return Collection<int, Branch>
     */
    public function list(User $admin): Collection
    {
        return Branch::query()
            ->with(['manager', 'bankAccount', 'cashDrawer'])
            ->where('organization_id', $admin->organization_id)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $admin, array $data): Branch
    {
        $branch = Branch::query()->create([
            'organization_id' => $admin->organization_id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(6)),
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'opening_hours' => $data['opening_hours'] ?? null,
            'receipt_footer' => $data['receipt_footer'] ?? null,
            'manager_user_id' => $data['manager_user_id'] ?? null,
            'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
            'settings' => $data['settings'] ?? null,
            'status' => 'open',
        ]);

        $drawer = CashDrawer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => $branch->name.' Drawer',
            'opening_balance' => (float) ($data['drawer_opening_balance'] ?? 0),
            'current_balance' => (float) ($data['drawer_opening_balance'] ?? 0),
            'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
        ]);

        $branch->update(['cash_drawer_id' => $drawer->id]);

        return $branch->fresh(['manager', 'bankAccount', 'cashDrawer']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $admin, Branch $branch, array $data): Branch
    {
        $this->authorize($admin, $branch);

        $branch->update([
            'name' => $data['name'],
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'opening_hours' => $data['opening_hours'] ?? null,
            'receipt_footer' => $data['receipt_footer'] ?? null,
            'manager_user_id' => $data['manager_user_id'] ?? null,
            'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
            'settings' => $data['settings'] ?? null,
        ]);

        if ($branch->cashDrawer && isset($data['drawer_opening_balance'])) {
            $branch->cashDrawer->update([
                'current_balance' => (float) $data['drawer_opening_balance'],
                'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
            ]);
        }

        return $branch->fresh(['manager', 'bankAccount', 'cashDrawer']);
    }

    /**
     * @return Collection<int, User>
     */
    public function managerOptions(User $admin): Collection
    {
        return User::query()
            ->where('organization_id', $admin->organization_id)
            ->whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'staff']))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Collection<int, FinanceBankAccount>
     */
    public function bankAccountOptions(User $admin): Collection
    {
        return FinanceBankAccount::query()
            ->where('organization_id', $admin->organization_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function authorize(User $admin, Branch $branch): void
    {
        if ((int) $branch->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }
}
