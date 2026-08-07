<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\BillStatus;
use App\Enums\RecurringBillFrequency;
use App\Events\RecurringBillGenerated;
use App\Models\RecurringBill;
use App\Models\User;
use App\Repositories\Contracts\BillRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class RecurringBillService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly BillRepositoryInterface $bills,
    ) {}

    /**
     * @return Collection<int, RecurringBill>
     */
    public function list(User $user): Collection
    {
        return RecurringBill::query()
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $id) => $q->where('branch_id', $id))
            ->orderBy('next_due_date')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data): RecurringBill
    {
        return RecurringBill::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $this->branchContext->currentBranchId($user),
            'title' => $data['title'],
            'vendor' => $data['vendor'] ?? null,
            'category' => $data['category'] ?? 'other',
            'amount' => $data['amount'],
            'frequency' => RecurringBillFrequency::from($data['frequency']),
            'next_due_date' => $data['next_due_date'],
            'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
            'status' => 'active',
            'created_by' => $user->id,
        ]);
    }

    public function generateDueBills(): int
    {
        $count = 0;
        $today = now()->toDateString();

        RecurringBill::query()
            ->where('status', 'active')
            ->whereDate('next_due_date', '<=', $today)
            ->each(function (RecurringBill $recurring) use (&$count): void {
                $bill = $this->bills->createBill([
                    'organization_id' => $recurring->organization_id,
                    'branch_id' => $recurring->branch_id,
                    'title' => $recurring->title,
                    'vendor' => $recurring->vendor,
                    'category' => $recurring->category ?? 'other',
                    'amount' => $recurring->amount,
                    'net_amount' => $recurring->amount,
                    'vat_amount' => 0,
                    'gross_amount' => $recurring->amount,
                    'due_date' => $recurring->next_due_date,
                    'status' => BillStatus::Pending->value,
                    'bank_account_id' => $recurring->finance_bank_account_id,
                    'created_by' => $recurring->created_by,
                ]);

                $recurring->update([
                    'last_generated_date' => $recurring->next_due_date,
                    'next_due_date' => $this->nextDueDate($recurring)->toDateString(),
                ]);

                RecurringBillGenerated::dispatch($recurring, $bill->id);
                $count++;
            });

        return $count;
    }

    private function nextDueDate(RecurringBill $recurring): Carbon
    {
        $base = Carbon::parse($recurring->next_due_date);

        return match ($recurring->frequency) {
            RecurringBillFrequency::Weekly => $base->addWeek(),
            RecurringBillFrequency::Monthly => $base->addMonth(),
            RecurringBillFrequency::Quarterly => $base->addMonths(3),
            RecurringBillFrequency::Yearly => $base->addYear(),
        };
    }
}
