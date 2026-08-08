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

        return $this->verifyPinForStaff($admin, $staff);
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function verifyPinForBranch(User $admin, string $pin, int $branchId): array
    {
        $staff = $this->requireStaffByPinForBranch($admin, $pin, $branchId);

        return $this->verifyPinForStaff($admin, $staff);
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
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    public function actionForBranch(User $admin, string $pin, string $action, int $branchId): array
    {
        $staff = $this->requireStaffByPinForBranch($admin, $pin, $branchId);
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
     * Smart kiosk: auto-select clock action from current attendance state.
     *
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float, action_performed: string, action_label: string}
     */
    public function smartKioskPin(User $admin, string $pin, int $branchId): array
    {
        $staff = $this->requireStaffByPinForBranch($admin, $pin, $branchId);
        $state = $this->resolveState($staff);
        $action = $this->resolveSmartKioskAction($state['state']);
        $this->clockAction($admin, $staff, $action);
        $result = $this->resolveState($staff);
        $result['action_performed'] = $action;
        $result['action_label'] = $this->smartActionLabel($action);

        return $result;
    }

    private function resolveSmartKioskAction(string $state): string
    {
        return match ($state) {
            'on_break' => 'end-break',
            'checked_in', 'auto_checked_in' => 'clock-out',
            default => 'clock-in',
        };
    }

    private function smartActionLabel(string $action): string
    {
        return match ($action) {
            'clock-in' => 'Clocked In',
            'clock-out' => 'Clocked Out',
            'start-break' => 'Break Started',
            'end-break' => 'Break Ended',
            default => 'Updated',
        };
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
     * @return array{from: Carbon, to: Carbon, sessions: list<array<string, mixed>>}
     */
    public function sessionRecords(User $admin, ?string $start = null): array
    {
        $from = $start ? Carbon::parse($start)->startOfWeek() : now()->startOfWeek();
        $to = $from->copy()->endOfWeek();
        $branchId = $this->branchContext->currentBranchId($admin);

        $logs = $this->attendance->logsForRange((int) $admin->organization_id, $branchId, $from, $to);
        $breaks = AttendanceBreak::query()
            ->with(['branchKiosk'])
            ->where('organization_id', $admin->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('break_started_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->get();

        $sessions = [];
        foreach ($logs->groupBy('user_id') as $userId => $userLogs) {
            $userBreaks = $breaks->where('user_id', (int) $userId);
            foreach ($userLogs->groupBy(fn ($log) => $log->logged_at->toDateString()) as $date => $dayLogs) {
                $sessions = array_merge(
                    $sessions,
                    $this->buildDaySessions($dayLogs, $userBreaks, (string) $date),
                );
            }
        }

        usort($sessions, fn (array $a, array $b) => [$b['date'], $a['user']->name] <=> [$a['date'], $b['user']->name]);

        return compact('from', 'to', 'sessions');
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionDetail(User $admin, int $userId, string $date): array
    {
        $member = $this->staff->findOrFail($userId);
        if ((int) $member->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        $day = Carbon::parse($date);
        $logs = $this->attendance->logsForUserOnDate($userId, $day)->load(['branch', 'branchKiosk']);
        $breaks = AttendanceBreak::query()
            ->with(['branchKiosk'])
            ->where('user_id', $userId)
            ->whereDate('break_started_at', $day->toDateString())
            ->orderBy('break_started_at')
            ->get();

        $sessions = $this->buildDaySessions($logs, $breaks, $day->toDateString());
        $shift = RotaShift::query()
            ->where('user_id', $userId)
            ->whereDate('shift_date', $day->toDateString())
            ->whereHas('rotaVersion', fn ($q) => $q->whereIn('status', ['published', 'locked']))
            ->first();

        return [
            'user' => $member,
            'date' => $day,
            'sessions' => $sessions,
            'logs' => $logs,
            'breaks' => $breaks,
            'shift' => $shift,
        ];
    }

    /**
     * @param  Collection<int, \App\Models\AttendanceLog>  $dayLogs
     * @param  Collection<int, AttendanceBreak>  $dayBreaks
     * @return list<array<string, mixed>>
     */
    private function buildDaySessions(Collection $dayLogs, Collection $dayBreaks, string $date): array
    {
        $sorted = $dayLogs->sortBy('logged_at')->values();
        $sessions = [];
        $inLog = null;

        foreach ($sorted as $log) {
            if ($log->type === AttendanceLogType::ClockIn) {
                $inLog = $log;
            } elseif ($log->type === AttendanceLogType::ClockOut && $inLog !== null) {
                $sessions[] = $this->sessionRow($inLog, $log, $dayBreaks, $date);
                $inLog = null;
            }
        }

        if ($inLog !== null) {
            $sessions[] = $this->sessionRow($inLog, null, $dayBreaks, $date);
        }

        return $sessions;
    }

    /**
     * @param  Collection<int, AttendanceBreak>  $dayBreaks
     * @return array<string, mixed>
     */
    private function sessionRow(\App\Models\AttendanceLog $inLog, ?\App\Models\AttendanceLog $outLog, Collection $dayBreaks, string $date): array
    {
        $in = $inLog->logged_at;
        $out = $outLog?->logged_at;
        $outAt = $out ?? now();
        $grossSeconds = $in->diffInSeconds($outAt);

        $sessionBreaks = $dayBreaks->filter(
            fn (AttendanceBreak $break) => $break->break_started_at >= $in
                && ($out === null || $break->break_started_at <= $out),
        );

        $breakSeconds = 0;
        $unpaidBreakSeconds = 0;
        foreach ($sessionBreaks as $break) {
            $end = $break->break_ended_at ?? $outAt;
            $secs = (int) $break->break_started_at->diffInSeconds($end);
            $breakSeconds += $secs;
            if (! $break->is_paid) {
                $unpaidBreakSeconds += $secs;
            }
        }

        $paidSeconds = max(0, $grossSeconds - $unpaidBreakSeconds);
        $hasActiveBreak = $sessionBreaks->contains(fn (AttendanceBreak $b) => $b->break_ended_at === null);

        $status = match (true) {
            $hasActiveBreak => 'on_break',
            $outLog === null => 'working',
            default => 'clocked_out',
        };

        return [
            'user' => $inLog->user,
            'branch' => $inLog->branch,
            'date' => $date,
            'clock_in' => $in->format('H:i'),
            'clock_out' => $out?->format('H:i'),
            'gross_hours' => round($grossSeconds / 3600, 2),
            'break_hours' => round($breakSeconds / 3600, 2),
            'paid_hours' => round($paidSeconds / 3600, 2),
            'status' => $status,
            'kiosk' => $inLog->branchKiosk?->name,
            'source' => $inLog->source instanceof \BackedEnum ? $inLog->source->value : (string) ($inLog->source ?? 'manual'),
            'breaks_count' => $sessionBreaks->count(),
            'clock_in_log_id' => $inLog->id,
        ];
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

    private function requireStaffByPinForBranch(User $admin, string $pin, int $branchId): User
    {
        $staff = $this->requireStaffByPin($admin, $pin);

        if ((int) $staff->branch_id !== $branchId) {
            throw ValidationException::withMessages(['pin' => 'This PIN is not valid for this branch.']);
        }

        return $staff;
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak, hours: ?float}
     */
    private function verifyPinForStaff(User $admin, User $staff): array
    {
        $state = $this->resolveState($staff);

        if ($state['state'] === 'not_checked_in' && $this->canClockIn($admin, $staff)) {
            $this->clockAction($admin, $staff, 'clock-in');
            $state = $this->resolveState($staff);
            $state['state'] = 'auto_checked_in';
        }

        return $state;
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

        $this->attendance->createBreak([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'break_type' => 'other',
            'break_started_at' => now(),
            'break_ended_at' => null,
            'is_paid' => false,
            'planned_minutes' => 30,
            'source' => 'manual',
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
            ->whereNull('break_ended_at')
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
            ->whereHas('rotaVersion', fn ($q) => $q->whereIn('status', ['published', 'locked']))
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
