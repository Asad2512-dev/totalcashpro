<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierInvoiceStatus;
use App\Models\ActivityLog;
use App\Models\CashUp;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\CashUpRepositoryInterface;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Carbon;

final class DashboardService implements ServiceInterface
{
    public function __construct(
        private readonly CashUpRepositoryInterface $cashUps,
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly StaffRepositoryInterface $staff,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly SupplierRepositoryInterface $suppliers,
        private readonly BranchContext $branchContext,
    ) {}

    public function build(User $user): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        $yearStart = now()->startOfYear();

        $todayCash = $this->cashUps->sumNetForRange($orgId, $branchId, $today, now());
        $weekCash = $this->cashUps->sumNetForRange($orgId, $branchId, $weekStart, now());
        $monthCash = $this->cashUps->sumNetForRange($orgId, $branchId, $monthStart, now());
        $yearCash = $this->cashUps->sumNetForRange($orgId, $branchId, $yearStart, now());

        $recentCashUps = $this->cashUps->forRange($orgId, $branchId, now()->subDays(14), now())->take(8);
        $lowStock = $this->inventory->lowStock($orgId, $branchId);
        $pendingSuppliers = $this->suppliers->pendingInvoiceCount($orgId, $branchId);
        $outstanding = $this->suppliers->outstandingAmount($orgId, $branchId);
        $clockedIn = $this->attendance->clockedInCountToday($orgId, $branchId);
        $staffCount = $this->staff->activeStaff($orgId, $branchId)->count();

        $attendanceToday = $this->attendance->logsForRange($orgId, $branchId, $today, now())
            ->groupBy('user_id')
            ->count();

        $cashChart = $this->currentMonthCashChart($orgId, $branchId, $monthStart, $today, $user);
        $cashChartPeriod = now()->format('F Y');

        $upcomingPayments = SupplierInvoice::query()
            ->with('supplier')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', SupplierInvoiceStatus::Pending->value)
            ->where('due_date', '>=', now()->toDateString())
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $orgUserIds = User::query()->where('organization_id', $orgId)->pluck('id');

        $recentActivity = ActivityLog::query()
            ->whereIn('actor_id', $orgUserIds)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (ActivityLog $log) => [
                'action' => $log->description ?? $log->event,
                'actor' => $log->actor_name ?: ($log->actor?->name ?? 'System'),
                'time' => $log->created_at?->diffForHumans(),
            ])
            ->all();

        return [
            'stats' => [
                ['label' => "Today's cash up", 'value' => $this->money($todayCash, $user), 'change' => 'Live', 'tone' => 'success'],
                ['label' => 'This week', 'value' => $this->money($weekCash, $user), 'change' => 'Cash up net', 'tone' => 'info'],
                ['label' => 'This month', 'value' => $this->money($monthCash, $user), 'change' => 'Cash up net', 'tone' => 'success'],
                ['label' => 'This year', 'value' => $this->money($yearCash, $user), 'change' => now()->format('Y').' YTD', 'tone' => 'info'],
                ['label' => 'Staff Clocked In', 'value' => (string) $clockedIn, 'change' => $staffCount.' active', 'tone' => 'info'],
                ['label' => 'Attendance Today', 'value' => (string) $attendanceToday, 'change' => 'Present', 'tone' => 'success'],
                ['label' => 'Inventory Alerts', 'value' => (string) $lowStock->count(), 'change' => 'Low stock', 'tone' => $lowStock->count() ? 'warning' : 'success'],
                ['label' => 'Pending Suppliers', 'value' => (string) $pendingSuppliers, 'change' => $this->money($outstanding, $user).' due', 'tone' => $pendingSuppliers ? 'warning' : 'success'],
            ],
            'cashChart' => $cashChart,
            'cashChartPeriod' => $cashChartPeriod,
            'cashChartTotal' => $this->money(
                array_sum(array_column($cashChart, 'amount')),
                $user,
            ).' · this month',
            'recentCashUps' => $recentCashUps,
            'lowStock' => $lowStock->take(6),
            'upcomingPayments' => $upcomingPayments,
            'recentActivity' => $recentActivity,
            'quickActions' => [
                ['label' => 'Daily Cash Up', 'route' => 'business-admin.cash-up', 'icon' => 'cash'],
                ['label' => 'Kiosk', 'route' => 'business-admin.kiosk.settings', 'icon' => 'clock'],
                ['label' => 'Add Staff', 'route' => 'business-admin.staff.create', 'icon' => 'users'],
                ['label' => 'Inventory', 'route' => 'business-admin.inventory', 'icon' => 'tag'],
            ],
        ];
    }

    /**
     * @return list<array{label: string, value: string, amount: float, height: int}>
     */
    private function currentMonthCashChart(int $orgId, ?int $branchId, Carbon $from, Carbon $to, User $user): array
    {
        $points = [];
        $chartHeight = 144;
        $day = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        $cashUpsByDate = $this->cashUps
            ->forRange($orgId, $branchId, $from, $to)
            ->groupBy(fn (CashUp $cashUp): string => $cashUp->cashup_date?->toDateString() ?? '');

        while ($day->lte($end)) {
            $dateKey = $day->toDateString();
            $dayCashUps = $cashUpsByDate->get($dateKey, collect());

            $coins = (float) $dayCashUps->sum('coins_total');
            $notes = (float) $dayCashUps->sum('notes_total');
            $cards = (float) $dayCashUps->sum('cards_total');
            $online = (float) $dayCashUps->sum('online_orders_total');
            $expenses = (float) $dayCashUps->sum('expenses_total');
            $deductions = (float) $dayCashUps->sum('platform_deductions_total');
            $gross = $coins + $notes + $cards + $online;
            $amount = $gross - $expenses - $deductions;
            $shiftCount = $dayCashUps->count();

            $points[] = [
                'label' => $day->format('j'),
                'date_label' => $day->format('j M Y'),
                'value' => $this->money($amount, $user),
                'amount' => $amount,
                'height' => 0,
                'details' => array_values(array_filter([
                    ['label' => 'Net cash up', 'value' => $this->money($amount, $user), 'emphasis' => true],
                    $shiftCount > 0 ? ['label' => 'Shifts', 'value' => (string) $shiftCount] : null,
                    $coins > 0 ? ['label' => 'Coins', 'value' => $this->money($coins, $user)] : null,
                    $notes > 0 ? ['label' => 'Notes', 'value' => $this->money($notes, $user)] : null,
                    $cards > 0 ? ['label' => 'Cards', 'value' => $this->money($cards, $user)] : null,
                    $online > 0 ? ['label' => 'Online', 'value' => $this->money($online, $user)] : null,
                    $expenses > 0 ? ['label' => 'Expenses', 'value' => '−'.$this->money($expenses, $user)] : null,
                    $deductions > 0 ? ['label' => 'Deductions', 'value' => '−'.$this->money($deductions, $user)] : null,
                ])),
            ];

            $day->addDay();
        }

        $max = max(array_column($points, 'amount') ?: [0.0]);
        if ($max <= 0) {
            return [];
        }

        return array_map(static function (array $point) use ($max, $chartHeight): array {
            $point['height'] = $point['amount'] > 0
                ? max(4, (int) round(($point['amount'] / $max) * $chartHeight))
                : 0;

            return $point;
        }, $points);
    }

    private function money(float $amount, User $user): string
    {
        $currency = $user->organization?->currency ?: 'GBP';
        $symbol = $currency === 'GBP' ? '£' : $currency.' ';

        return $symbol.number_format($amount, 2);
    }
}
