<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashDrawerMovementType;
use App\Enums\CashUpShift;
use App\Enums\CashUpStatus;
use App\Models\CashDrawer;
use App\Models\CashUp;
use App\Models\User;
use App\Repositories\Contracts\CashUpRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CashUpService implements ServiceInterface
{
    public const COINS = [
        ['coin' => '£2', 'value' => 2.00],
        ['coin' => '£1', 'value' => 1.00],
        ['coin' => '50p', 'value' => 0.50],
        ['coin' => '20p', 'value' => 0.20],
        ['coin' => '10p', 'value' => 0.10],
        ['coin' => '5p', 'value' => 0.05],
        ['coin' => '2p', 'value' => 0.02],
        ['coin' => '1p', 'value' => 0.01],
    ];

    public const NOTES = [
        ['note' => '£50', 'value' => 50.00, 'is_qty' => true],
        ['note' => '£20', 'value' => 20.00, 'is_qty' => true],
        ['note' => '£10', 'value' => 10.00, 'is_qty' => true],
        ['note' => '£5', 'value' => 5.00, 'is_qty' => true],
    ];

    public const OPENING_FLOAT_DENOMINATIONS = [
        ['label' => '£20', 'value' => 20.00],
        ['label' => '£10', 'value' => 10.00],
        ['label' => '£5', 'value' => 5.00],
        ['label' => '£2', 'value' => 2.00],
        ['label' => '£1', 'value' => 1.00],
        ['label' => '50p', 'value' => 0.50],
        ['label' => '20p', 'value' => 0.20],
        ['label' => '10p', 'value' => 0.10],
        ['label' => '5p', 'value' => 0.05],
    ];

    public const PLATFORMS = ['Uber Eats', 'Just Eat', 'Deliveroo', 'Foodhub'];

    public function __construct(
        private readonly CashUpRepositoryInterface $cashUps,
        private readonly BranchContext $branchContext,
        private readonly CashCountingService $counting,
        private readonly CashReconciliationService $reconciliation,
        private readonly CashMovementService $movements,
        private readonly CashDrawerService $drawers,
    ) {}

    public function findOrEmpty(User $user, string $date, string $shift, ?int $drawerId = null): ?CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);

        return $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $date,
            $shift,
            $drawerId,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(User $user, array $payload, bool $overwrite = false): CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $shift = CashUpShift::from($payload['shift']);

        $existing = $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $payload['cashup_date'],
            $shift->value,
            isset($payload['cash_drawer_id']) ? (int) $payload['cash_drawer_id'] : null,
        );

        if ($existing?->isLocked()) {
            throw ValidationException::withMessages([
                'cashup' => 'This cash up is locked and cannot be edited.',
            ]);
        }

        $drawer = $this->resolveDrawer($user, $payload, $branchId);
        $openingFloat = (float) ($payload['opening_float'] ?? $drawer?->defaultOpeningFloat() ?? config('cash.default_opening_float', 100));
        $openingFloatCount = $this->normalizeOpeningFloatCount($payload['opening_float_count'] ?? []);

        if (! empty($openingFloatCount)) {
            $countedFloat = $this->counting->sumDenominationRows($openingFloatCount);
            if (abs($countedFloat - $openingFloat) > 0.009 && empty($payload['float_adjustment_reason'])) {
                throw ValidationException::withMessages([
                    'opening_float' => sprintf(
                        'Opening float is £%s %s. Confirm or adjust with a reason.',
                        number_format(abs($countedFloat - $openingFloat), 2),
                        $countedFloat < $openingFloat ? 'short' : 'over',
                    ),
                ]);
            }
            $openingFloat = $countedFloat;
        }

        $coinsDetail = $this->normalizeQtyRows($payload['coins'] ?? [], 'coin');
        $notesDetail = $this->normalizeNoteRows($payload['notes'] ?? []);
        $cardsDetail = $this->normalizeCardRows($payload['cards'] ?? []);
        $expensesDetail = $this->normalizeExpenseRows($payload['expenses'] ?? []);
        $onlineDetail = $this->normalizeAmountRows($payload['online'] ?? [], 'platform');

        $coinsTotal = $this->sumCoinRows($coinsDetail);
        $notesTotal = $this->sumNoteRows($notesDetail);
        $actualCash = round($coinsTotal + $notesTotal, 2);
        $cashExpenses = (float) collect($expensesDetail)->sum(fn ($row) => (float) ($row['amount'] ?? 0));

        $reconciled = $this->reconciliation->reconcile(
            openingFloat: $openingFloat,
            actualCash: $actualCash,
            cashExpenses: $cashExpenses,
            cashSales: array_key_exists('cash_sales_total', $payload) ? (float) $payload['cash_sales_total'] : null,
        );

        $variance = $reconciled['variance'];
        $threshold = $drawer?->varianceThreshold() ?? (float) config('cash.default_variance_threshold', 0);
        if ($this->reconciliation->requiresVarianceReason($variance, $threshold) && empty($payload['variance_reason'])) {
            throw ValidationException::withMessages([
                'variance_reason' => 'Variance exceeds the acceptable threshold. Please provide a reason.',
            ]);
        }

        $attributes = [
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'cash_drawer_id' => $drawer?->id,
            'cashup_date' => $payload['cashup_date'],
            'shift' => $shift->value,
            'opening_float' => $openingFloat,
            'opening_float_count' => $openingFloatCount ?: null,
            'cash_sales_total' => $reconciled['cash_sales'],
            'coins_detail' => $coinsDetail,
            'coins_total' => $coinsTotal,
            'notes_detail' => $notesDetail,
            'notes_total' => $notesTotal,
            'cards_detail' => $cardsDetail,
            'cards_total' => $this->sumCardRows($cardsDetail),
            'expenses_detail' => $expensesDetail,
            'expenses_total' => $cashExpenses,
            'online_orders_detail' => $onlineDetail,
            'online_orders_total' => collect($onlineDetail)->sum(fn ($row) => (float) ($row['amount'] ?? 0)),
            'expected_cash' => $reconciled['expected_cash'],
            'actual_cash' => $reconciled['actual_cash'],
            'variance' => $variance,
            'variance_reason' => $payload['variance_reason'] ?? null,
            'status' => ($payload['submit'] ?? false) ? CashUpStatus::Submitted->value : CashUpStatus::Draft->value,
            'created_by' => $user->id,
        ];

        if ($existing !== null) {
            $attributes['platform_deductions_detail'] = $existing->platform_deductions_detail;
            $attributes['platform_deductions_total'] = $existing->platform_deductions_total;
        }

        return DB::transaction(function () use ($user, $attributes, $overwrite, $drawer, $expensesDetail, $reconciled): CashUp {
            $cashUp = $this->cashUps->upsertForShift($attributes, $overwrite);

            if ($drawer !== null) {
                $this->syncDrawerMovements($user, $drawer, $cashUp, $expensesDetail, $reconciled['cash_sales']);
                $drawer->update([
                    'last_cash_up_at' => now(),
                    'updated_by' => $user->id,
                ]);
            }

            return $cashUp->refresh();
        });
    }

    public function submit(User $user, CashUp $cashUp): CashUp
    {
        $this->assertCashUpAccess($user, $cashUp);

        if ($cashUp->isLocked()) {
            throw ValidationException::withMessages(['cashup' => 'Cash up is locked.']);
        }

        $cashUp->update(['status' => CashUpStatus::Submitted->value]);

        return $cashUp->refresh();
    }

    public function approve(User $user, CashUp $cashUp): CashUp
    {
        $this->assertCashUpAccess($user, $cashUp);

        $cashUp->update([
            'status' => CashUpStatus::Approved->value,
            'approved_by_user_id' => $user->id,
            'approved_at' => now(),
            'locked_at' => now(),
        ]);

        return $cashUp->refresh();
    }

    public function reject(User $user, CashUp $cashUp, ?string $reason = null): CashUp
    {
        $this->assertCashUpAccess($user, $cashUp);

        $cashUp->update([
            'status' => CashUpStatus::Rejected->value,
            'variance_reason' => $reason ?? $cashUp->variance_reason,
            'locked_at' => null,
        ]);

        return $cashUp->refresh();
    }

    public function reopen(User $user, CashUp $cashUp): CashUp
    {
        $this->assertCashUpAccess($user, $cashUp);

        $cashUp->update([
            'status' => CashUpStatus::Draft->value,
            'approved_by_user_id' => null,
            'approved_at' => null,
            'locked_at' => null,
        ]);

        return $cashUp->refresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function saveDeductions(User $user, array $payload, bool $overwrite = false): CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $drawerId = isset($payload['cash_drawer_id']) ? (int) $payload['cash_drawer_id'] : null;
        $existing = $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $payload['cashup_date'],
            $payload['shift'],
            $drawerId,
        );

        if ($existing?->isLocked()) {
            throw ValidationException::withMessages(['cashup' => 'This cash up is locked.']);
        }

        $deductionsDetail = $this->normalizeAmountRows($payload['deductions'] ?? [], 'platform');
        $attributes = [
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'cashup_date' => $payload['cashup_date'],
            'shift' => $payload['shift'],
            'platform_deductions_detail' => $deductionsDetail,
            'platform_deductions_total' => collect($deductionsDetail)->sum(fn ($row) => (float) ($row['amount'] ?? 0)),
            'created_by' => $user->id,
        ];

        if ($existing === null) {
            $drawer = $this->resolveDrawer($user, $payload, $branchId);

            return $this->cashUps->create(array_merge([
                'cash_drawer_id' => $drawer?->id,
                'coins_total' => 0,
                'notes_total' => 0,
                'cards_total' => 0,
                'expenses_total' => 0,
                'online_orders_total' => 0,
                'opening_float' => $drawer?->defaultOpeningFloat($user->organization) ?? config('cash.default_opening_float', 100),
            ], $attributes));
        }

        if (! $overwrite && (float) $existing->platform_deductions_total > 0) {
            throw ValidationException::withMessages([
                'cashup' => 'Platform deductions already exist for this shift. Confirm overwrite to replace them.',
            ]);
        }

        $existing->update($attributes);

        return $existing->refresh();
    }

    public function history(User $user, string $period, ?string $date = null, ?int $drawerId = null, ?string $status = null): array
    {
        $anchor = Carbon::parse($date ?: now()->toDateString());
        $branchId = $this->branchContext->currentBranchId($user);

        [$from, $to] = match ($period) {
            'weekly' => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
            'monthly' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            'yearly' => [$anchor->copy()->startOfYear(), $anchor->copy()->endOfYear()],
            default => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
        };

        $rows = $this->cashUps->forRange((int) $user->organization_id, $branchId, $from, $to)
            ->when($drawerId, fn ($c) => $c->where('cash_drawer_id', $drawerId))
            ->when($status, fn ($c) => $c->where('status', $status));

        return [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'total' => $rows->sum(fn (CashUp $c) => $c->revenueTotal()),
        ];
    }

    /**
     * @param  list<array{description: string, amount: float}>  $expenses
     */
    private function syncDrawerMovements(User $user, CashDrawer $drawer, CashUp $cashUp, array $expenses, float $cashSales): void
    {
        $drawer->transactions()->where('cash_up_id', $cashUp->id)->delete();

        if ($cashSales > 0) {
            $this->movements->record(
                $user,
                $drawer,
                CashDrawerMovementType::Sale,
                $cashSales,
                sprintf('Cash sales · %s %s', $cashUp->cashup_date?->format('d M Y'), $cashUp->shift?->value ?? $cashUp->shift),
                $drawer->openSession,
                referenceType: CashUp::class,
                referenceId: $cashUp->id,
            )->update(['cash_up_id' => $cashUp->id]);
        }

        foreach ($expenses as $expense) {
            $amount = (float) ($expense['amount'] ?? 0);
            if ($amount <= 0) {
                continue;
            }

            $this->movements->record(
                $user,
                $drawer,
                CashDrawerMovementType::Expense,
                $amount,
                (string) ($expense['description'] ?? 'Cash expense'),
                $drawer->openSession,
                referenceType: CashUp::class,
                referenceId: $cashUp->id,
            )->update(['cash_up_id' => $cashUp->id]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveDrawer(User $user, array $payload, int $branchId): ?CashDrawer
    {
        $activeTills = CashDrawer::query()
            ->where('organization_id', $user->organization_id)
            ->where('branch_id', $branchId)
            ->where('status', \App\Enums\CashDrawerStatus::Active->value)
            ->where('is_active', true)
            ->count();

        if (empty($payload['cash_drawer_id'])) {
            if ($activeTills > 0) {
                throw ValidationException::withMessages([
                    'cash_drawer_id' => 'Select a till for this cash up.',
                ]);
            }

            return null;
        }

        $drawer = $this->drawers->findForBranch($user, (int) $payload['cash_drawer_id']);

        if ((int) $drawer->branch_id !== $branchId) {
            throw ValidationException::withMessages(['cash_drawer_id' => 'Till does not belong to this branch.']);
        }

        if (! $drawer->isUsableForCashUp()) {
            throw ValidationException::withMessages(['cash_drawer_id' => 'This till is not active. Select another till.']);
        }

        return $drawer;
    }

    private function assertCashUpAccess(User $user, CashUp $cashUp): void
    {
        if ((int) $cashUp->organization_id !== (int) $user->organization_id) {
            abort(403);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{label: string, qty: int, value: float}>
     */
    private function normalizeOpeningFloatCount(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $qty = (int) ($row['qty'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $normalized[] = [
                'label' => (string) ($row['label'] ?? $row['note'] ?? $row['coin'] ?? ''),
                'qty' => $qty,
                'value' => (float) ($row['value'] ?? 0),
            ];
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{coin: string, qty: int}>
     */
    private function normalizeQtyRows(array $rows, string $key): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $qty = (int) ($row['qty'] ?? 0);
            if ($qty <= 0 || empty($row[$key])) {
                continue;
            }
            $normalized[] = [$key => (string) $row[$key], 'qty' => $qty];
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function normalizeNoteRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            if (empty($row['note'])) {
                continue;
            }

            $qty = (int) ($row['qty'] ?? 0);
            if ($qty > 0) {
                $normalized[] = ['note' => (string) $row['note'], 'qty' => $qty, 'is_qty' => true];
            }
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{payment_type: string, type: string, amount: float}>
     */
    private function normalizeCardRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $amount = (float) ($row['amount'] ?? 0);
            if ($amount <= 0) {
                continue;
            }
            $type = ($row['type'] ?? 'machine') === 'refund' ? 'refund' : 'machine';
            $normalized[] = [
                'payment_type' => (string) ($row['payment_type'] ?? ($type === 'refund' ? 'Refunds' : 'Card Machine')),
                'type' => $type,
                'amount' => $amount,
            ];
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{description: string, amount: float}>
     */
    private function normalizeExpenseRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $amount = (float) ($row['amount'] ?? 0);
            $description = trim((string) ($row['description'] ?? ''));
            if ($amount <= 0 || $description === '') {
                continue;
            }
            $normalized[] = ['description' => $description, 'amount' => $amount];
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{platform: string, amount: float}>
     */
    private function normalizeAmountRows(array $rows, string $key): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $amount = (float) ($row['amount'] ?? 0);
            if ($amount <= 0 || empty($row[$key])) {
                continue;
            }
            $normalized[] = [$key => (string) $row[$key], 'amount' => $amount];
        }

        return $normalized;
    }

    /**
     * @param  list<array{coin: string, qty: int}>  $rows
     */
    private function sumCoinRows(array $rows): float
    {
        return $this->counting->sumCoins($rows, self::COINS);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function sumNoteRows(array $rows): float
    {
        return $this->counting->sumNotes($rows, self::NOTES, excludeFloat: true);
    }

    /**
     * @param  list<array{type: string, amount: float}>  $rows
     */
    private function sumCardRows(array $rows): float
    {
        $total = 0.0;
        foreach ($rows as $row) {
            $amount = (float) ($row['amount'] ?? 0);
            $total += ($row['type'] ?? 'machine') === 'refund' ? -$amount : $amount;
        }

        return round($total, 2);
    }
}
