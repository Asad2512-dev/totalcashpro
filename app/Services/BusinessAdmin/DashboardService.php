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

        $todayCash = $this->cashUps->sumNetForRange($orgId, $branchId, $today, now());
        $weekCash = $this->cashUps->sumNetForRange($orgId, $branchId, $weekStart, now());
        $monthCash = $this->cashUps->sumNetForRange($orgId, $branchId, $monthStart, now());

        $recentCashUps = $this->cashUps->forRange($orgId, $branchId, now()->subDays(14), now())->take(8);
        $lowStock = $this->inventory->lowStock($orgId, $branchId);
        $pendingSuppliers = $this->suppliers->pendingInvoiceCount($orgId, $branchId);
        $outstanding = $this->suppliers->outstandingAmount($orgId, $branchId);
        $clockedIn = $this->attendance->clockedInCountToday($orgId, $branchId);
        $staffCount = $this->staff->activeStaff($orgId, $branchId)->count();

        $attendanceToday = $this->attendance->logsForRange($orgId, $branchId, $today, now())
            ->groupBy('user_id')
            ->count();

        $revenueBars = $this->dailyBars($orgId, $branchId, 14);

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
                ['label' => "Today's Cash", 'value' => $this->money($todayCash, $user), 'change' => 'Live', 'tone' => 'success'],
                ['label' => 'Weekly Revenue', 'value' => $this->money($weekCash, $user), 'change' => 'This week', 'tone' => 'info'],
                ['label' => 'Monthly Revenue', 'value' => $this->money($monthCash, $user), 'change' => 'This month', 'tone' => 'success'],
                ['label' => 'Staff Clocked In', 'value' => (string) $clockedIn, 'change' => $staffCount.' active', 'tone' => 'info'],
                ['label' => 'Attendance Today', 'value' => (string) $attendanceToday, 'change' => 'Present', 'tone' => 'success'],
                ['label' => 'Inventory Alerts', 'value' => (string) $lowStock->count(), 'change' => 'Low stock', 'tone' => $lowStock->count() ? 'warning' : 'success'],
                ['label' => 'Pending Suppliers', 'value' => (string) $pendingSuppliers, 'change' => $this->money($outstanding, $user).' due', 'tone' => $pendingSuppliers ? 'warning' : 'success'],
            ],
            'revenueBars' => $revenueBars,
            'recentCashUps' => $recentCashUps,
            'lowStock' => $lowStock->take(6),
            'upcomingPayments' => $upcomingPayments,
            'recentActivity' => $recentActivity,
            'quickActions' => [
                ['label' => 'Daily Cash Up', 'route' => 'business-admin.cash-up', 'icon' => 'cash'],
                ['label' => 'Clock In', 'route' => 'business-admin.clock-in', 'icon' => 'clock'],
                ['label' => 'Add Staff', 'route' => 'business-admin.staff.create', 'icon' => 'users'],
                ['label' => 'Inventory', 'route' => 'business-admin.inventory', 'icon' => 'tag'],
            ],
        ];
    }

    /**
     * @return list<int> bar heights as percentages for chart-card
     */
    private function dailyBars(int $orgId, ?int $branchId, int $days): array
    {
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $values[] = $this->cashUps->sumNetForRange($orgId, $branchId, $day, $day->copy()->endOfDay());
        }

        $max = max($values ?: [0]);
        if ($max <= 0) {
            return [];
        }

        return array_map(
            fn (float $value): int => max(8, (int) round(($value / $max) * 100)),
            $values,
        );
    }

    private function money(float $amount, User $user): string
    {
        $currency = $user->organization?->currency ?: 'GBP';
        $symbol = $currency === 'GBP' ? '£' : $currency.' ';

        return $symbol.number_format($amount, 2);
    }
}
