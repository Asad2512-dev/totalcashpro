<?php

declare(strict_types=1);

namespace App\Services\Staff;

use App\Contracts\ServiceInterface;
use App\Enums\LeaveType;
use App\Enums\NotificationCategory;
use App\Enums\RequestStatus;
use App\Enums\RoleSlug;
use App\Models\AppNotification;
use App\Models\LeaveRequest;
use App\Models\RotaShift;
use App\Models\ShiftSwapRequest;
use App\Models\StaffAvailability;
use App\Models\User;
use App\Services\BusinessAdmin\AttendanceService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class StaffHrService implements ServiceInterface
{
    public function __construct(private readonly AttendanceService $attendance) {}

    /**
     * @return Collection<int, StaffAvailability>
     */
    public function availability(User $staff): Collection
    {
        $this->assertStaff($staff);

        return StaffAvailability::query()
            ->where('user_id', $staff->id)
            ->orderBy('day_of_week')
            ->get();
    }

    /**
     * @param  list<array{day_of_week: int, start_time?: ?string, end_time?: ?string, is_available: bool}>  $rows
     */
    public function saveAvailability(User $staff, array $rows): void
    {
        $this->assertStaff($staff);

        foreach ($rows as $row) {
            StaffAvailability::query()->updateOrCreate(
                ['user_id' => $staff->id, 'day_of_week' => (int) $row['day_of_week']],
                [
                    'organization_id' => $staff->organization_id,
                    'branch_id' => $staff->branch_id,
                    'start_time' => $row['start_time'] ?? null,
                    'end_time' => $row['end_time'] ?? null,
                    'is_available' => (bool) ($row['is_available'] ?? true),
                ],
            );
        }
    }

    /**
     * @return Collection<int, LeaveRequest>
     */
    public function leaveRequests(User $staff): Collection
    {
        $this->assertStaff($staff);

        return LeaveRequest::query()
            ->where('user_id', $staff->id)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function submitLeave(User $staff, array $data): LeaveRequest
    {
        $this->assertStaff($staff);

        if ($data['start_date'] > $data['end_date']) {
            throw ValidationException::withMessages(['end_date' => 'End date must be on or after start date.']);
        }

        $request = LeaveRequest::query()->create([
            'user_id' => $staff->id,
            'organization_id' => $staff->organization_id,
            'branch_id' => $staff->branch_id,
            'type' => LeaveType::from($data['type']),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => RequestStatus::Pending,
            'reason' => $data['reason'] ?? null,
        ]);

        $this->notifyManagers($staff, 'Leave request submitted', "{$staff->name} requested {$request->type->label()} leave.", NotificationCategory::Staff);

        return $request;
    }

    /**
     * @return Collection<int, ShiftSwapRequest>
     */
    public function shiftSwapRequests(User $staff): Collection
    {
        $this->assertStaff($staff);

        return ShiftSwapRequest::query()
            ->with(['rotaShift', 'targetUser'])
            ->where('requester_id', $staff->id)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function submitShiftSwap(User $staff, array $data): ShiftSwapRequest
    {
        $this->assertStaff($staff);

        $shift = RotaShift::query()->findOrFail((int) $data['rota_shift_id']);
        if ((int) $shift->user_id !== (int) $staff->id) {
            throw ValidationException::withMessages(['rota_shift_id' => 'You can only request swaps for your own shifts.']);
        }

        $swap = ShiftSwapRequest::query()->create([
            'organization_id' => $staff->organization_id,
            'branch_id' => $staff->branch_id,
            'requester_id' => $staff->id,
            'target_user_id' => $data['target_user_id'] ?? null,
            'rota_shift_id' => $shift->id,
            'status' => RequestStatus::Pending,
            'reason' => $data['reason'] ?? null,
        ]);

        $this->notifyManagers($staff, 'Shift swap requested', "{$staff->name} requested a shift swap.", NotificationCategory::Staff);

        return $swap;
    }

    /**
     * @return array{from: \Illuminate\Support\Carbon, to: \Illuminate\Support\Carbon, days: array<string, array{hours: float, slots: list<array{in: string, out: string}>}>, totalHours: float}
     */
    public function weeklyHours(User $staff, ?string $start = null): array
    {
        $week = $this->attendance->personalWeek($staff, $start);
        $week['totalHours'] = round(collect($week['days'])->sum('hours'), 2);

        return $week;
    }

    /**
     * @return list<RotaShift>
     */
    public function upcomingSwappableShifts(User $staff): array
    {
        $this->assertStaff($staff);

        return RotaShift::query()
            ->where('user_id', $staff->id)
            ->whereDate('shift_date', '>=', now()->toDateString())
            ->whereHas('rotaVersion', fn ($q) => $q->whereIn('status', ['published', 'locked']))
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->limit(20)
            ->get()
            ->all();
    }

    private function assertStaff(User $staff): void
    {
        if (! $staff->isStaff() || $staff->organization_id === null) {
            abort(403);
        }
    }

    private function notifyManagers(User $staff, string $title, string $body, NotificationCategory $category): void
    {
        $admins = User::query()
            ->where('organization_id', $staff->organization_id)
            ->whereHas('role', fn ($q) => $q->where('slug', RoleSlug::Admin->value))
            ->get();

        foreach ($admins as $admin) {
            AppNotification::query()->create([
                'user_id' => $admin->id,
                'title' => $title,
                'body' => $body,
                'type' => 'staff_request',
                'category' => $category->value,
                'data' => ['staff_id' => $staff->id],
            ]);
        }
    }
}
