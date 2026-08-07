<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\BillStatus;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\Spending;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use Illuminate\Support\Carbon;

final class AccountingService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
    ) {}

    /**
     * @return array<string, string>
     */
    public function billCategories(): array
    {
        return [
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'insurance' => 'Insurance',
            'software' => 'Software & subscriptions',
            'tax' => 'Tax & compliance',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function spendingCategories(): array
    {
        return [
            'supplies' => 'Supplies',
            'marketing' => 'Marketing',
            'maintenance' => 'Maintenance & repairs',
            'travel' => 'Travel',
            'food' => 'Food & beverage',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function paymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank' => 'Bank transfer',
            'other' => 'Other',
        ];
    }

    /**
     * @return array{
     *     revenue: float,
     *     payroll_out: float,
     *     supplier_bills: float,
     *     bills_due: float,
     *     spendings: float,
     *     net_position: float
     * }
     */
    public function overview(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from ??= now()->startOfMonth();
        $to ??= now()->endOfMonth();

        $cashQuery = CashUp::query()
            ->where('organization_id', $orgId)
            ->whereBetween('cashup_date', [$from->toDateString(), $to->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $revenue = (float) $cashQuery->get()->sum(fn (CashUp $c) => $c->netTotal());

        $payrollOut = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->where('status', WageStatus::Paid->value)
            ->whereBetween('paid_date', [$from->toDateString(), $to->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $supplierBills = (float) SupplierInvoice::query()
            ->where('organization_id', $orgId)
            ->where('status', SupplierInvoiceStatus::Paid->value)
            ->whereBetween('paid_date', [$from->toDateString(), $to->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $billsDue = (float) Bill::query()
            ->where('organization_id', $orgId)
            ->whereIn('status', [BillStatus::Approved->value, BillStatus::Pending->value, BillStatus::Overdue->value])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $spendings = (float) Spending::query()
            ->where('organization_id', $orgId)
            ->whereBetween('spent_date', [$from->toDateString(), $to->toDateString()])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $outflows = $payrollOut + $supplierBills + $spendings;

        return [
            'revenue' => $revenue,
            'payroll_out' => $payrollOut,
            'supplier_bills' => $supplierBills,
            'bills_due' => $billsDue,
            'spendings' => $spendings,
            'net_position' => $revenue - $outflows,
        ];
    }

    public function listBills(User $user): mixed
    {
        $this->syncOverdueBills($user);

        return Bill::query()
            ->with('branch')
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $branchId) => $q->where('branch_id', $branchId))
            ->orderBy('due_date')
            ->paginate(20)
            ->withQueryString();
    }

    public function listSpendings(User $user): mixed
    {
        return Spending::query()
            ->with('branch')
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $branchId) => $q->where('branch_id', $branchId))
            ->orderByDesc('spent_date')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeBill(User $user, array $data): Bill
    {
        $branchId = $this->branchContext->requireBranchId($user);

        $amounts = \App\Support\Finance\VatCalculator::fromGross((float) $data['amount'], 20);

        return Bill::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'title' => $data['title'],
            'vendor' => $data['vendor'] ?? null,
            'category' => $data['category'] ?? 'other',
            'amount' => $amounts['gross'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'due_date' => $data['due_date'],
            'status' => BillStatus::Draft->value,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeSpending(User $user, array $data): Spending
    {
        $branchId = $this->branchContext->requireBranchId($user);

        $amounts = \App\Support\Finance\VatCalculator::fromGross((float) $data['amount'], 20);

        return Spending::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'title' => $data['title'],
            'category' => $data['category'] ?? 'other',
            'amount' => $amounts['gross'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'status' => FinanceStatus::Draft->value,
            'spent_date' => $data['spent_date'],
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);
    }

    public function markBillPaid(User $user, int $billId): Bill
    {
        $bill = Bill::query()->findOrFail($billId);

        if ((int) $bill->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $bill->update([
            'status' => BillStatus::Paid->value,
            'paid_date' => now()->toDateString(),
        ]);

        return $bill;
    }

    private function syncOverdueBills(User $user): void
    {
        Bill::query()
            ->where('organization_id', $user->organization_id)
            ->whereIn('status', [BillStatus::Approved->value, BillStatus::Pending->value])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => BillStatus::Overdue->value]);
    }
}
