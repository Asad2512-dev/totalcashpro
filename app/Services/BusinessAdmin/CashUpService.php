<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashUpShift;
use App\Models\CashUp;
use App\Models\User;
use App\Repositories\Contracts\CashUpRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

final class CashUpService implements ServiceInterface
{
    public const COINS = [
        ['coin' => '£1', 'value' => 1.00],
        ['coin' => '50p', 'value' => 0.50],
        ['coin' => '20p', 'value' => 0.20],
        ['coin' => '10p', 'value' => 0.10],
        ['coin' => '5p', 'value' => 0.05],
        ['coin' => '1p', 'value' => 0.01],
    ];

    public const NOTES = [
        ['note' => '£50', 'value' => 50.00, 'is_qty' => true],
        ['note' => '£20', 'value' => 20.00, 'is_qty' => true],
        ['note' => '£10', 'value' => 10.00, 'is_qty' => true],
        ['note' => '£5', 'value' => 5.00, 'is_qty' => true],
        ['note' => 'Extra Coin (Float)', 'value' => 1.00, 'is_qty' => false],
    ];

    public const PLATFORMS = ['Uber Eats', 'Just Eat', 'Deliveroo', 'Foodhub'];

    public function __construct(
        private readonly CashUpRepositoryInterface $cashUps,
        private readonly BranchContext $branchContext,
    ) {}

    public function findOrEmpty(User $user, string $date, string $shift): ?CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);

        return $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $date,
            $shift,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(User $user, array $payload, bool $overwrite = false): CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $shift = CashUpShift::from($payload['shift']);

        $coinsDetail = $this->normalizeQtyRows($payload['coins'] ?? [], 'coin');
        $notesDetail = $this->normalizeNoteRows($payload['notes'] ?? [], (float) ($payload['extra_float'] ?? 0));
        $cardsDetail = $this->normalizeCardRows($payload['cards'] ?? []);
        $expensesDetail = $this->normalizeExpenseRows($payload['expenses'] ?? []);
        $onlineDetail = $this->normalizeAmountRows($payload['online'] ?? [], 'platform');

        $attributes = [
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'cashup_date' => $payload['cashup_date'],
            'shift' => $shift->value,
            'coins_detail' => $coinsDetail,
            'coins_total' => $this->sumCoinRows($coinsDetail),
            'notes_detail' => $notesDetail,
            'notes_total' => $this->sumNoteRows($notesDetail),
            'cards_detail' => $cardsDetail,
            'cards_total' => $this->sumCardRows($cardsDetail),
            'expenses_detail' => $expensesDetail,
            'expenses_total' => collect($expensesDetail)->sum(fn ($row) => (float) ($row['amount'] ?? 0)),
            'online_orders_detail' => $onlineDetail,
            'online_orders_total' => collect($onlineDetail)->sum(fn ($row) => (float) ($row['amount'] ?? 0)),
            'created_by' => $user->id,
        ];

        // Preserve existing platform deductions when saving the wizard.
        $existing = $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $payload['cashup_date'],
            $shift->value,
        );
        if ($existing !== null) {
            $attributes['platform_deductions_detail'] = $existing->platform_deductions_detail;
            $attributes['platform_deductions_total'] = $existing->platform_deductions_total;
        }

        return $this->cashUps->upsertForShift($attributes, $overwrite);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function saveDeductions(User $user, array $payload, bool $overwrite = false): CashUp
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $existing = $this->cashUps->findByDateShift(
            (int) $user->organization_id,
            $branchId,
            $payload['cashup_date'],
            $payload['shift'],
        );

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
            return $this->cashUps->create(array_merge([
                'coins_total' => 0,
                'notes_total' => 0,
                'cards_total' => 0,
                'expenses_total' => 0,
                'online_orders_total' => 0,
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

    public function history(User $user, string $period, ?string $date = null): array
    {
        $anchor = Carbon::parse($date ?: now()->toDateString());
        $branchId = $this->branchContext->currentBranchId($user);

        [$from, $to] = match ($period) {
            'weekly' => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
            'monthly' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
        };

        $rows = $this->cashUps->forRange((int) $user->organization_id, $branchId, $from, $to);

        return [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'total' => $rows->sum(fn (CashUp $c) => $c->netTotal()),
        ];
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
    private function normalizeNoteRows(array $rows, float $extraFloat): array
    {
        $normalized = [];
        $hasFloat = false;

        foreach ($rows as $row) {
            if (empty($row['note'])) {
                continue;
            }

            $isQty = array_key_exists('is_qty', $row)
                ? (bool) $row['is_qty']
                : ! str_contains((string) $row['note'], 'Float');

            if ($isQty) {
                $qty = (int) ($row['qty'] ?? 0);
                if ($qty > 0) {
                    $normalized[] = ['note' => (string) $row['note'], 'qty' => $qty, 'is_qty' => true];
                }
            } else {
                $amount = (float) ($row['amount'] ?? $row['qty'] ?? 0);
                if ($amount > 0) {
                    $normalized[] = ['note' => (string) $row['note'], 'amount' => $amount, 'is_qty' => false];
                    $hasFloat = true;
                }
            }
        }

        if (! $hasFloat && $extraFloat > 0) {
            $normalized[] = ['note' => 'Extra Coin (Float)', 'amount' => $extraFloat, 'is_qty' => false];
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
        $map = collect(self::COINS)->keyBy('coin');

        return round((float) collect($rows)->sum(function (array $row) use ($map): float {
            return ((float) ($map[$row['coin']]['value'] ?? 0)) * (int) $row['qty'];
        }), 2);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function sumNoteRows(array $rows): float
    {
        $map = collect(self::NOTES)->keyBy('note');

        return round((float) collect($rows)->sum(function (array $row) use ($map): float {
            if (($row['is_qty'] ?? true) === false) {
                return (float) ($row['amount'] ?? 0);
            }

            return ((float) ($map[$row['note']]['value'] ?? 0)) * (int) ($row['qty'] ?? 0);
        }), 2);
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
