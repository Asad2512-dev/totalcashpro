<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\AttendanceLogType;
use App\Models\AttendanceBreak;
use App\Models\RotaShift;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class AttendanceService implements ServiceInterface
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly StaffRepositoryInterface $staff,
        private readonly BranchContext $branchContext,
    ) {}

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function verifyPin(User $admin, string $pin): array
    {
        $staff = $this->requireStaffByPin($admin, $pin);
        $state = $this->resolveState($staff);

        if ($state['state'] === 'not_checked_in' && $this->canClockIn($admin, $staff)) {
            $this->clockAction($admin, $staff, 'clock-in');
            $state = $this->resolveState($staff);
            $state['state'] = 'auto_checked_in';
        }

        return $state;
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function action(User $admin, string $pin, string $action): array
    {
        $staff = $this->requireStaffByPin($admin, $pin);
        $this->clockAction($admin, $staff, $action);

        return $this->resolveState($staff);
    }

    /**
     * Staff self-service clock state (authenticated staff user, no PIN).
     *
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function currentStateForStaff(User $staff): array
    {
        $this->assertStaffActor($staff);

        return $this->resolveState($staff);
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function actionForStaff(User $staff, string $action): array
    {
        $this->assertStaffActor($staff);
        $this->clockAction($staff, $staff, $action);

        return $this->resolveState($staff);
    }

    /**
     * @return array{from: Carbon, to: Carbon, days: array<string, array{hours: float, slots: list<array{in: string, out: string}>}>}
     */
    public function personalWeek(User $staff, ?string $start = null): array
    {
        $this->assertStaffActor($staff);
        $from = $start ? Carbon::parse($start)->startOfWeek() : now()->startOfWeek();
        $to = $from->copy()->endOfWeek();
        $branchId = $staff->branch_id ? (int) $staff->branch_id : null;
        $logs = $this->attendance->logsForRange((int) $staff->organization_id, $branchId, $from, $to)
            ->where('user_id', $staff->id)
            ->values();

        $days = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $dayLogs = $logs->filter(fn ($log) => $log->logged_at->isSameDay($day))->values();
            $days[$day->toDateString()] = [
                'hours' => $this->hoursFromLogs($dayLogs),
                'slots' => $this->pairSlots($dayLogs),
            ];
        }

        return compact('from', 'to', 'days');
    }

    private function assertStaffActor(User $staff): void
    {
        if (! $staff->isStaff() || $staff->organization_id === null) {
            abort(403);
        }
    }

    public function weeklyReport(User $admin, ?string $start = null): array
    {
        $from = $start ? Carbon::parse($start)->startOfWeek() : now()->startOfWeek();
        $to = $from->copy()->endOfWeek();
        $branchId = $this->branchContext->currentBranchId($admin);
        $staff = $this->staff->activeStaff((int) $admin->organization_id, $branchId);
        $logs = $this->attendance->logsForRange((int) $admin->organization_id, $branchId, $from, $to);

        $report = [];
        foreach ($staff as $member) {
            $days = [];
            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $dayLogs = $logs->where('user_id', $member->id)
                    ->filter(fn ($log) => $log->logged_at->isSameDay($day))
                    ->values();
                $hours = $this->hoursFromLogs($dayLogs);
                $days[$day->toDateString()] = [
                    'hours' => $hours,
                    'pay' => round($hours * (float) ($member->hourly_rate ?? 0), 2),
                    'slots' => $this->pairSlots($dayLogs),
                ];
            }
            $report[] = [
                'user' => $member,
                'days' => $days,
                'total_hours' => collect($days)->sum('hours'),
                'total_pay' => collect($days)->sum('pay'),
            ];
        }

        return compact('from', 'to', 'report');
    }

    /**
     * @param  list<array{in: string, out: string}>  $slots
     */
    public function replaceDayEntries(User $admin, int $userId, string $date, array $slots): void
    {
        $member = $this->staff->findOrFail($userId);
        if ((int) $member->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        $branchId = (int) ($member->branch_id ?: $this->branchContext->requireBranchId($admin));
        $day = Carbon::parse($date);
        $this->attendance->deleteUserLogsOnDate($userId, $day);

        foreach ($slots as $slot) {
            if (empty($slot['in']) || empty($slot['out'])) {
                continue;
            }
            $this->attendance->createLog([
                'organization_id' => $admin->organization_id,
                'branch_id' => $branchId,
                'user_id' => $userId,
                'type' => AttendanceLogType::ClockIn->value,
                'logged_at' => Carbon::parse($day->toDateString().' '.$slot['in']),
            ]);
            $this->attendance->createLog([
                'organization_id' => $admin->organization_id,
                'branch_id' => $branchId,
                'user_id' => $userId,
                'type' => AttendanceLogType::ClockOut->value,
                'logged_at' => Carbon::parse($day->toDateString().' '.$slot['out']),
            ]);
        }
    }

    private function requireStaffByPin(User $admin, string $pin): User
    {
        $staff = $this->staff->findByPin((int) $admin->organization_id, $pin);

        if ($staff === null) {
            throw ValidationException::withMessages(['pin' => 'Invalid PIN.']);
        }

        return $staff;
    }

    private function clockAction(User $admin, User $staff, string $action): void
    {
        $branchId = (int) ($staff->branch_id ?: $this->branchContext->requireBranchId($admin));
        $state = $this->resolveState($staff);

        match ($action) {
            'clock-in' => $this->doClockIn($admin, $staff, $branchId, $state),
            'clock-out' => $this->doClockOut($admin, $staff, $branchId, $state),
            'start-break' => $this->doStartBreak($admin, $staff, $branchId, $state),
            'end-break' => $this->doEndBreak($staff),
            default => throw ValidationException::withMessages(['action' => 'Unknown clock action.']),
        };
    }

    /** @param  array{state: string}  $state */
    private function doClockIn(User $admin, User $staff, int $branchId, array $state): void
    {
        if ($state['state'] !== 'not_checked_in') {
            throw ValidationException::withMessages(['action' => 'Already clocked in.']);
        }

        if (! $this->canClockIn($admin, $staff)) {
            throw ValidationException::withMessages(['action' => 'Clock-in is not allowed right now.']);
        }

        $this->attendance->createLog([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'type' => AttendanceLogType::ClockIn->value,
            'logged_at' => now(),
        ]);
    }

    /** @param  array{state: string}  $state */
    private function doClockOut(User $admin, User $staff, int $branchId, array $state): void
    {
        if (! in_array($state['state'], ['checked_in', 'on_break'], true)) {
            throw ValidationException::withMessages(['action' => 'Not currently clocked in.']);
        }

        if ($state['state'] === 'on_break') {
            $this->doEndBreak($staff);
        }

        $this->attendance->createLog([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'type' => AttendanceLogType::ClockOut->value,
            'logged_at' => now(),
        ]);
    }

    /** @param  array{state: string}  $state */
    private function doStartBreak(User $admin, User $staff, int $branchId, array $state): void
    {
        if ($state['state'] !== 'checked_in') {
            throw ValidationException::withMessages(['action' => 'Must be clocked in to start a break.']);
        }

        // Legacy: schedule break end 30 minutes ahead.
        $this->attendance->createBreak([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'break_started_at' => now(),
            'break_ended_at' => now()->addMinutes(30),
        ]);
    }

    private function doEndBreak(User $staff): void
    {
        $break = $this->currentBreak($staff);

        if ($break === null) {
            throw ValidationException::withMessages(['action' => 'No active break.']);
        }

        $break->update(['break_ended_at' => now()]);
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    private function resolveState(User $staff): array
    {
        $logs = $this->attendance->logsForUserOnDate($staff->id, now());
        $last = $logs->last();
        $break = $this->currentBreak($staff);

        if ($break !== null) {
            return ['user' => $staff, 'state' => 'on_break', 'break' => $break, 'hours' => null];
        }

        if ($last === null || $last->type === AttendanceLogType::ClockOut) {
            return [
                'user' => $staff,
                'state' => 'not_checked_in',
                'break' => null,
                'hours' => $last ? $this->hoursFromLogs($logs) : null,
            ];
        }

        return ['user' => $staff, 'state' => 'checked_in', 'break' => null, 'hours' => null];
    }

    private function currentBreak(User $staff): ?AttendanceBreak
    {
        return AttendanceBreak::query()
            ->where('user_id', $staff->id)
            ->whereDate('break_started_at', now()->toDateString())
            ->where('break_started_at', '<=', now())
            ->where('break_ended_at', '>', now())
            ->latest('break_started_at')
            ->first();
    }

    private function canClockIn(User $admin, User $staff): bool
    {
        $settings = $admin->organization?->settings ?? [];
        $strictRota = (string) ($settings['strict_rota_clockin'] ?? '0') !== '0';

        if (! $strictRota) {
            return true;
        }

        return RotaShift::query()
            ->where('user_id', $staff->id)
            ->whereDate('shift_date', now()->toDateString())
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->exists();
    }

    /** @param  Collection<int, \App\Models\AttendanceLog>  $logs */
    private function hoursFromLogs(Collection $logs): float
    {
        $seconds = 0;
        $in = null;
        foreach ($logs as $log) {
            if ($log->type === AttendanceLogType::ClockIn) {
                $in = $log->logged_at;
            } elseif ($log->type === AttendanceLogType::ClockOut && $in !== null) {
                $seconds += $in->diffInSeconds($log->logged_at);
                $in = null;
            }
        }

        return round($seconds / 3600, 2);
    }

    /**
     * @param  Collection<int, \App\Models\AttendanceLog>  $logs
     * @return list<array{in: string, out: ?string}>
     */
    private function pairSlots(Collection $logs): array
    {
        $slots = [];
        $in = null;
        foreach ($logs as $log) {
            if ($log->type === AttendanceLogType::ClockIn) {
                $in = $log->logged_at->format('H:i');
            } elseif ($log->type === AttendanceLogType::ClockOut && $in !== null) {
                $slots[] = ['in' => $in, 'out' => $log->logged_at->format('H:i')];
                $in = null;
            }
        }

        // Still clocked in (no clock-out yet).
        if ($in !== null) {
            $slots[] = ['in' => $in, 'out' => null];
        }

        return $slots;
    }
}
