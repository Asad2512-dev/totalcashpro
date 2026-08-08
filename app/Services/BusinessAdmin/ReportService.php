<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\CashUp;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use App\Repositories\Contracts\CashUpRepositoryInterface;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Support\Carbon;

final class ReportService implements ServiceInterface
{
    public function __construct(
        private readonly CashUpRepositoryInterface $cashUps,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly StaffRepositoryInterface $staff,
        private readonly BranchContext $branchContext,
    ) {}

    public function aggregate(User $user, string $period, string $from, string $to): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $cashRows = $this->cashUps->forRange($orgId, $branchId, $fromDate, $toDate);
        $totalRevenue = (float) $cashRows->sum(fn (CashUp $c) => $c->revenueTotal());

        $totalWages = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');

        $totalSupplierInvoices = (float) SupplierInvoice::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', SupplierInvoiceStatus::Pending->value)
            ->whereBetween('invoice_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->sum('amount');

        $paidWages = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', WageStatus::Paid->value)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');

        $profit = $totalRevenue - $totalWages - $totalSupplierInvoices;
        $activeStaffCount = $this->staff->activeStaff($orgId, $branchId)->count();
        $lowStockCount = $this->inventory->lowStock($orgId, $branchId)->count();

        $dailyRevenue = [];
        $cursor = $fromDate->copy();
        $days = min(62, (int) $fromDate->diffInDays($toDate) + 1);
        for ($i = 0; $i < $days; $i++) {
            $day = $cursor->copy()->addDays($i);
            $dayEnd = $day->copy()->endOfDay();
            $dailyRevenue[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('D, M j'),
                'revenue' => $this->cashUps->sumNetForRange($orgId, $branchId, $day, $dayEnd),
            ];
        }

        $chartBars = $this->toChartBars(array_column($dailyRevenue, 'revenue'));

        return [
            'totalRevenue' => $totalRevenue,
            'totalWages' => $totalWages,
            'paidWages' => $paidWages,
            'totalSupplierInvoices' => $totalSupplierInvoices,
            'profit' => $profit,
            'activeStaffCount' => $activeStaffCount,
            'lowStockCount' => $lowStockCount,
            'dailyRevenue' => $dailyRevenue,
            'cashRows' => $cashRows,
            'chartBars' => $chartBars,
            'period' => $period,
        ];
    }

    /**
     * @param  list<float|int>  $values
     * @return list<int>
     */
    private function toChartBars(array $values): array
    {
        $max = max($values ?: [0]);
        if ($max <= 0) {
            return [];
        }

        return array_map(
            fn ($value): int => max(8, (int) round(((float) $value / $max) * 100)),
            $values,
        );
    }
}
