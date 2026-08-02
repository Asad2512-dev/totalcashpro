<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\WageStatus;
use App\Models\User;
use App\Models\Wage;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Contracts\WageRepositoryInterface;

final class PayrollService implements ServiceInterface
{
    public function __construct(
        private readonly WageRepositoryInterface $wages,
        private readonly StaffRepositoryInterface $staff,
        private readonly BranchContext $branchContext,
    ) {}

    public function list(User $user, string $period = 'current'): mixed
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        $query = Wage::query()
            ->with('user')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        if ($period === 'unpaid') {
            $query->where('status', WageStatus::Pending->value);
        } elseif ($period === 'paid') {
            $query->where('status', WageStatus::Paid->value);
        }

        return $query->latest('created_at')->paginate(20)->withQueryString();
    }

    public function formMeta(User $user): array
    {
        $branchId = $this->branchContext->currentBranchId($user);

        return [
            'staff' => $this->staff->activeStaff((int) $user->organization_id, $branchId),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data): Wage
    {
        $staff = User::query()->findOrFail((int) $data['user_id']);

        if ((int) $staff->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $branchId = (int) ($staff->branch_id ?: $this->branchContext->requireBranchId($user));
        $hours = (float) $data['hours_worked'];
        $rate = (float) ($data['hourly_rate'] ?? $staff->hourly_rate ?? 0);
        $amount = round($hours * $rate, 2);

        return Wage::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'hours_worked' => $hours,
            'amount' => $amount,
            'notes' => $data['notes'] ?? null,
            'status' => WageStatus::Pending->value,
            'created_by' => $user->id,
        ]);
    }

    public function markPaid(User $user, int $wageId): Wage
    {
        $wage = Wage::query()->findOrFail($wageId);

        if ((int) $wage->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $wage->update([
            'status' => WageStatus::Paid->value,
            'paid_date' => now()->toDateString(),
        ]);

        return $wage;
    }
}
