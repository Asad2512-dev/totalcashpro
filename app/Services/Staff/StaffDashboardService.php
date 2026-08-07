<?php

declare(strict_types=1);

namespace App\Services\Staff;

use App\Contracts\ServiceInterface;
use App\Models\AppNotification;
use App\Models\CashUp;
use App\Models\RotaShift;
use App\Models\User;
use App\Services\BusinessAdmin\AttendanceService;
use Illuminate\Support\Collection;

final class StaffDashboardService implements ServiceInterface
{
    public function __construct(private readonly AttendanceService $attendance) {}

    /**
     * @return array{
     *     state: array{user: User, state: string, break: mixed, hours: ?float},
     *     todayShift: ?RotaShift,
     *     branchName: string,
     *     recentCashUps: Collection<int, CashUp>,
     *     notifications: Collection<int, AppNotification>
     * }
     */
    public function overview(User $staff): array
    {
        $staff->loadMissing(['branch', 'organization']);

        $todayShift = RotaShift::query()
            ->with('rotaSection')
            ->where('user_id', $staff->id)
            ->whereDate('shift_date', now()->toDateString())
            ->orderBy('start_time')
            ->first();

        $recentCashUps = CashUp::query()
            ->where('organization_id', $staff->organization_id)
            ->when($staff->branch_id, fn ($q) => $q->where('branch_id', $staff->branch_id))
            ->latest('cashup_date')
            ->limit(5)
            ->get();

        $notifications = AppNotification::query()
            ->where('user_id', $staff->id)
            ->latest()
            ->limit(5)
            ->get();

        $week = $this->attendance->personalWeek($staff);

        return [
            'state' => $this->attendance->currentStateForStaff($staff),
            'todayShift' => $todayShift,
            'branchName' => $staff->branch?->name ?? 'Unassigned',
            'weeklyHours' => round(collect($week['days'])->sum('hours'), 2),
            'recentCashUps' => $recentCashUps,
            'notifications' => $notifications,
        ];
    }
}
