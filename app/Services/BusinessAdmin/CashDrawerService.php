<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashDrawerStatus;
use App\Models\CashDrawer;
use App\Models\CashDrawerSession;
use App\Models\CashDrawerTransaction;
use App\Models\CashUp;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CashDrawerService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly CashDrawerSessionService $sessions,
    ) {}

    /**
     * @return Collection<int, CashDrawer>
     */
    public function list(User $user, bool $withOpenSession = true, ?int $branchId = null): Collection
    {
        $query = CashDrawer::query()
            ->with(['branch', 'assignedUser', 'lastCashUp.creator'])
            ->where('organization_id', $user->organization_id)
            ->when($branchId ?? $this->branchContext->currentBranchId($user), fn ($q, $id) => $q->where('branch_id', $id))
            ->orderBy('branch_id')
            ->orderBy('name');

        if ($withOpenSession) {
            $query->with('openSession.openedBy');
        }

        return $query->get();
    }

    public function dashboard(User $user, ?int $branchId = null): array
    {
        $drawers = $this->list($user, true, $branchId);

        return [
            'drawers' => $drawers,
            'summary' => [
                'total' => $drawers->count(),
                'active' => $drawers->filter(fn (CashDrawer $d) => $d->drawerStatus() === CashDrawerStatus::Active)->count(),
                'inactive' => $drawers->filter(fn (CashDrawer $d) => $d->drawerStatus() === CashDrawerStatus::Inactive)->count(),
                'total_cash' => round($drawers->sum(fn (CashDrawer $d) => (float) $d->current_balance), 2),
                'today_variance' => round($drawers->sum(function (CashDrawer $d): float {
                    $last = $d->lastCashUp;

                    return $last && $last->cashup_date?->isToday() ? $last->varianceAmount() : 0.0;
                }), 2),
            ],
        ];
    }

    public function create(User $user, array $data): CashDrawer
    {
        $branchId = (int) ($data['branch_id'] ?? $this->branchContext->requireBranchId($user));
        $organization = $user->organization ?? Organization::query()->findOrFail($user->organization_id);
        $defaultFloat = (float) ($data['default_opening_float'] ?? $this->organizationDefaultFloat($organization));
        $code = $this->normalizeCode($data['code'] ?? null, $branchId, $user->organization_id);

        $this->assertUniqueCode($user->organization_id, $branchId, $code);

        return DB::transaction(function () use ($user, $data, $branchId, $defaultFloat, $code): CashDrawer {
            $drawer = CashDrawer::query()->create([
                'organization_id' => $user->organization_id,
                'branch_id' => $branchId,
                'name' => $data['name'],
                'code' => $code,
                'opening_balance' => $defaultFloat,
                'current_balance' => $defaultFloat,
                'currency' => $data['currency'] ?? config('cash.currency', 'GBP'),
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'status' => CashDrawerStatus::Active->value,
                'notes' => $data['notes'] ?? null,
                'settings' => [
                    'default_opening_float' => $defaultFloat,
                    'variance_threshold' => (float) ($data['variance_threshold'] ?? config('cash.default_variance_threshold', 0)),
                ],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            return $drawer;
        });
    }

    public function update(User $user, CashDrawer $drawer, array $data): CashDrawer
    {
        $this->assertAccess($user, $drawer);

        if (isset($data['code']) && $data['code'] !== $drawer->code) {
            $code = $this->normalizeCode($data['code'], $drawer->branch_id, $drawer->organization_id);
            $this->assertUniqueCode($drawer->organization_id, $drawer->branch_id, $code, $drawer->id);
            $data['code'] = $code;
        }

        $settings = array_merge($drawer->settings ?? [], array_filter([
            'default_opening_float' => isset($data['default_opening_float'])
                ? (float) $data['default_opening_float']
                : null,
            'variance_threshold' => isset($data['variance_threshold'])
                ? (float) $data['variance_threshold']
                : null,
        ], fn ($v) => $v !== null));

        $drawer->update([
            'name' => $data['name'] ?? $drawer->name,
            'code' => $data['code'] ?? $drawer->code,
            'assigned_user_id' => array_key_exists('assigned_user_id', $data)
                ? $data['assigned_user_id']
                : $drawer->assigned_user_id,
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : $drawer->is_active,
            'status' => $data['status'] ?? ($drawer->status instanceof CashDrawerStatus ? $drawer->status->value : $drawer->status),
            'notes' => array_key_exists('notes', $data) ? $data['notes'] : $drawer->notes,
            'settings' => $settings,
            'updated_by' => $user->id,
        ]);

        return $drawer->refresh();
    }

    public function setStatus(User $user, CashDrawer $drawer, CashDrawerStatus $status): CashDrawer
    {
        $this->assertAccess($user, $drawer);

        if ($status === CashDrawerStatus::Archived && $drawer->cashUps()->exists()) {
            throw ValidationException::withMessages([
                'status' => 'Cannot archive a till with cash up history. Deactivate instead.',
            ]);
        }

        $drawer->update([
            'status' => $status->value,
            'is_active' => in_array($status, [CashDrawerStatus::Active, CashDrawerStatus::Locked], true),
            'updated_by' => $user->id,
        ]);

        return $drawer->refresh();
    }

    public function detail(User $user, CashDrawer $drawer, ?string $period = 'daily', ?string $date = null): array
    {
        $this->assertAccess($user, $drawer);

        $anchor = Carbon::parse($date ?: now()->toDateString());
        [$from, $to] = match ($period) {
            'weekly' => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
            'monthly' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
        };

        $movements = CashDrawerTransaction::query()
            ->with('creator')
            ->where('cash_drawer_id', $drawer->id)
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        $cashUps = CashUp::query()
            ->with('creator')
            ->where('cash_drawer_id', $drawer->id)
            ->whereBetween('cashup_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('cashup_date')
            ->orderBy('shift')
            ->get();

        return [
            'drawer' => $drawer->load(['branch', 'assignedUser', 'lastCashUp.creator']),
            'movements' => $movements,
            'cashUps' => $cashUps,
            'period' => $period,
            'from' => $from,
            'to' => $to,
        ];
    }

    public function organizationDefaultFloat(Organization $organization): float
    {
        $orgFloat = (float) data_get($organization->settings, 'cash.default_opening_float', 0);

        return $orgFloat > 0 ? $orgFloat : (float) config('cash.default_opening_float', 100);
    }

    public function updateOpeningBalance(User $user, CashDrawer $drawer, float $balance): CashDrawer
    {
        $this->assertAccess($user, $drawer);

        if (! $user->isAdmin()) {
            abort(403, 'Only business admins can adjust opening float.');
        }

        $drawer->update([
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'updated_by' => $user->id,
        ]);

        return $drawer->refresh();
    }

    public function openDrawer(
        User $user,
        CashDrawer $drawer,
        ?float $openingFloat = null,
        ?array $openingCount = null,
    ): CashDrawerSession {
        if (! $drawer->isUsableForCashUp()) {
            throw ValidationException::withMessages(['drawer' => 'Till is not active.']);
        }

        return $this->sessions->open($user, $drawer, $openingFloat, $openingCount);
    }

    public function closeDrawer(
        User $user,
        CashDrawerSession $session,
        float $actualCash,
        float $expectedCash,
        ?string $varianceReason = null,
    ): CashDrawerSession {
        return $this->sessions->close($user, $session, $actualCash, $expectedCash, null, $varianceReason);
    }

    public function findForBranch(User $user, int $drawerId): CashDrawer
    {
        $drawer = CashDrawer::query()
            ->where('organization_id', $user->organization_id)
            ->whereKey($drawerId)
            ->firstOrFail();

        $branchId = $this->branchContext->currentBranchId($user);
        if ($branchId !== null && (int) $drawer->branch_id !== $branchId) {
            abort(403);
        }

        return $drawer;
    }

    private function assertAccess(User $user, CashDrawer $drawer): void
    {
        if ((int) $drawer->organization_id !== (int) $user->organization_id) {
            abort(403);
        }
    }

    private function assertUniqueCode(int $organizationId, int $branchId, ?string $code, ?int $ignoreId = null): void
    {
        if ($code === null || $code === '') {
            return;
        }

        $exists = CashDrawer::query()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->where('code', $code)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['code' => 'Till code already exists for this branch.']);
        }
    }

    private function normalizeCode(?string $code, int $branchId, int $organizationId): ?string
    {
        if ($code !== null && $code !== '') {
            return strtoupper(trim($code));
        }

        $count = CashDrawer::query()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->count();

        return sprintf('TILL-%02d', $count + 1);
    }
}
