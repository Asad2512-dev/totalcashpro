<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\AttendanceLogType;
use App\Enums\AttendanceSource;
use App\Enums\BreakType;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\BranchKiosk;
use App\Models\KioskBreakType;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Support\Kiosk\KioskContext;
use App\Support\Security\StaffPinHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class KioskAttendanceService implements ServiceInterface
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendance,
        private readonly StaffRepositoryInterface $staff,
        private readonly KioskConfigurationService $config,
        private readonly RotaEnforcementService $rota,
        private readonly KioskBreakService $breaks,
        private readonly KioskBreakTypeService $breakTypes,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function authenticatePinForContext(KioskContext $context, string $pin): array
    {
        $staff = $this->requireStaffByContext($context, $pin);
        $settings = $context->settings;
        $state = $this->resolveState($staff);

        if ($state['state'] === 'not_checked_in') {
            if (! ($settings['allow_clock_in'] ?? true)) {
                throw ValidationException::withMessages(['pin' => 'Clock-in is disabled on this kiosk.']);
            }

            $result = $this->clockInForContext($context, $staff);

            return array_merge($result, ['step' => 'success']);
        }

        if ($state['state'] === 'on_break') {
            $breakLabel = $this->breakLabelFor($state['break']);

            return [
                'step' => 'on_break',
                'user' => $this->staffPayload($staff),
                'state' => $state['state'],
                'message' => "Currently on {$breakLabel}.",
                'actions' => [
                    ['action' => 'end-break', 'label' => 'Return to Work'],
                ],
                'active_break' => $this->breakPayload($state['break']),
            ];
        }

        $actions = [];
        if ($settings['allow_clock_out'] ?? true) {
            $actions[] = ['action' => 'clock-out', 'label' => 'Clock Off'];
        }
        if (($settings['allow_breaks'] ?? true) && count($this->config->enabledBreakTypesForOrganization($context->organizationId)) > 0) {
            $actions[] = ['action' => 'choose-break', 'label' => 'Take a Break'];
        }

        return [
            'step' => 'choose_action',
            'user' => $this->staffPayload($staff),
            'state' => $state['state'],
            'message' => 'What would you like to do?',
            'actions' => $actions,
            'break_options' => $this->config->enabledBreakTypesForOrganization($context->organizationId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function performActionForContext(
        KioskContext $context,
        string $pin,
        string $action,
        ?string $breakType = null,
        ?string $idempotencyKey = null,
    ): array {
        $staff = $this->requireStaffByContext($context, $pin);
        $key = $idempotencyKey ?? Str::uuid()->toString();

        if ($action === 'choose-break') {
            return [
                'step' => 'choose_break',
                'user' => $this->staffPayload($staff),
                'break_options' => $this->config->enabledBreakTypesForOrganization($context->organizationId),
            ];
        }

        return match ($action) {
            'clock-out' => array_merge($this->clockOutForContext($context, $staff, $key), ['step' => 'success']),
            'start-break' => array_merge($this->startBreakForContext($context, $staff, (string) $breakType, $key), ['step' => 'success']),
            'end-break' => array_merge($this->endBreakForContext($context, $staff, $key), ['step' => 'success']),
            default => throw ValidationException::withMessages(['action' => 'Unknown action.']),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function clockInForContext(KioskContext $context, User $staff, ?string $idempotencyKey = null): array
    {
        $settings = $context->settings;
        if (! ($settings['allow_clock_in'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Clock-in is disabled on this kiosk.']);
        }

        $state = $this->resolveState($staff);
        if ($state['state'] !== 'not_checked_in') {
            throw ValidationException::withMessages(['action' => 'Already clocked in.']);
        }

        if ($idempotencyKey !== null) {
            $existing = $this->findExistingLog($idempotencyKey);
            if ($existing !== null) {
                return $this->successPayload($staff, 'clock-in', 'Clocked in successfully');
            }
        }

        return DB::transaction(function () use ($context, $staff, $idempotencyKey): array {
            $state = $this->resolveState($staff);
            if ($state['state'] !== 'not_checked_in') {
                throw ValidationException::withMessages(['action' => 'Already clocked in.']);
            }

            $this->attendance->createLog([
                'organization_id' => $context->organizationId,
                'branch_id' => $context->branchId,
                'user_id' => $staff->id,
                'branch_kiosk_id' => null,
                'type' => AttendanceLogType::ClockIn->value,
                'source' => AttendanceSource::Kiosk->value,
                'logged_at' => now(),
                'idempotency_key' => $idempotencyKey,
            ]);

            return $this->successPayload($staff, 'clock-in', 'Clocked in successfully');
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function clockOutForContext(KioskContext $context, User $staff, ?string $idempotencyKey = null): array
    {
        $settings = $context->settings;
        if (! ($settings['allow_clock_out'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Clock-out is disabled on this kiosk.']);
        }

        $state = $this->resolveState($staff);
        if ($state['state'] === 'on_break') {
            throw ValidationException::withMessages(['action' => 'Return from your break before clocking off.']);
        }

        if ($state['state'] !== 'checked_in') {
            throw ValidationException::withMessages(['action' => 'Not currently clocked in.']);
        }

        if ($idempotencyKey !== null) {
            $existing = $this->findExistingLog($idempotencyKey);
            if ($existing !== null) {
                return $this->successPayload($staff, 'clock-out', 'Clocked off successfully', [
                    'net_minutes' => $this->netWorkingMinutesToday($staff),
                ]);
            }
        }

        return DB::transaction(function () use ($context, $staff, $idempotencyKey): array {
            $state = $this->resolveState($staff);
            if ($state['state'] !== 'checked_in') {
                throw ValidationException::withMessages(['action' => 'Not currently clocked in.']);
            }

            $this->attendance->createLog([
                'organization_id' => $context->organizationId,
                'branch_id' => $context->branchId,
                'user_id' => $staff->id,
                'branch_kiosk_id' => null,
                'type' => AttendanceLogType::ClockOut->value,
                'source' => AttendanceSource::Kiosk->value,
                'logged_at' => now(),
                'idempotency_key' => $idempotencyKey,
            ]);

            return $this->successPayload($staff, 'clock-out', 'Clocked off successfully', [
                'net_minutes' => $this->netWorkingMinutesToday($staff),
                'net_label' => $this->formatMinutes($this->netWorkingMinutesToday($staff)),
            ]);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function startBreakForContext(KioskContext $context, User $staff, string $breakType, ?string $idempotencyKey = null): array
    {
        $settings = $context->settings;
        if (! ($settings['allow_breaks'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Breaks are disabled on this kiosk.']);
        }

        $type = $this->breakTypes->findActive($context->organizationId, $breakType);
        if ($type === null) {
            throw ValidationException::withMessages(['action' => 'This break type is not available.']);
        }

        $state = $this->resolveState($staff);
        if ($state['state'] !== 'checked_in') {
            throw ValidationException::withMessages(['action' => 'Must be clocked in to start a break.']);
        }

        return DB::transaction(function () use ($context, $staff, $type, $idempotencyKey): array {
            $state = $this->resolveState($staff);
            if ($state['state'] !== 'checked_in') {
                throw ValidationException::withMessages(['action' => 'Must be clocked in to start a break.']);
            }

            $break = $this->breaks->startForContext($context, $staff, $type);

            return $this->successPayload($staff, 'start-break', $type->name.' break started', [
                'break' => $this->breakPayload($break),
            ]);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function endBreakForContext(KioskContext $context, User $staff, ?string $idempotencyKey = null): array
    {
        $state = $this->resolveState($staff);
        if ($state['state'] !== 'on_break' || $state['break'] === null) {
            throw ValidationException::withMessages(['action' => 'No active break.']);
        }

        return DB::transaction(function () use ($staff, $state): array {
            $break = $this->breaks->end($state['break']);
            $label = $this->breakLabelFor($break);

            return $this->successPayload($staff, 'end-break', $label.' break ended', [
                'break' => $this->breakPayload($break),
            ]);
        });
    }

    /**
     * Authenticate PIN and determine next kiosk step.
     *
     * @return array<string, mixed>
     */
    public function authenticatePin(BranchKiosk $kiosk, string $pin): array
    {
        $staff = $this->requireStaffByPin($kiosk, $pin);
        $settings = $this->config->forKiosk($kiosk);
        $state = $this->resolveState($staff);

        if ($state['state'] === 'not_checked_in') {
            if (! ($settings['allow_clock_in'] ?? true)) {
                throw ValidationException::withMessages(['pin' => 'Clock-in is disabled on this kiosk.']);
            }

            $rota = $this->rota->evaluateClockIn($staff, $kiosk, $this->config);

            if (! $rota['allowed']) {
                return [
                    'step' => 'rota_restricted',
                    'user' => $this->staffPayload($staff),
                    'state' => $state['state'],
                    'rota' => $rota,
                    'actions' => [
                        ['action' => 'clock-in-override', 'label' => 'Admin Override'],
                    ],
                ];
            }

            $result = $this->clockIn($kiosk, $staff);

            return array_merge($result, [
                'step' => 'success',
                'rota_warning' => $rota['message'],
            ]);
        }

        if ($state['state'] === 'on_break') {
            $breakLabel = $this->resolveBreakType($state['break']?->break_type)->label();

            return [
                'step' => 'choose_action',
                'user' => $this->staffPayload($staff),
                'state' => $state['state'],
                'message' => "You're currently on {$breakLabel}.",
                'actions' => $this->actionsForOnBreak($kiosk, $settings),
                'active_break' => $this->breakPayload($state['break']),
            ];
        }

        if (($settings['allow_breaks'] ?? true) && ($settings['allow_clock_out'] ?? true)) {
            return [
                'step' => 'choose_action',
                'user' => $this->staffPayload($staff),
                'state' => $state['state'],
                'message' => 'What would you like to do?',
                'actions' => $this->actionsForCheckedIn($kiosk, $settings),
            ];
        }

        if (! ($settings['allow_clock_out'] ?? true)) {
            throw ValidationException::withMessages(['pin' => 'Clock-out is disabled on this kiosk.']);
        }

        $result = $this->clockOut($kiosk, $staff);

        return array_merge($result, ['step' => 'success']);
    }

    /**
     * @return array<string, mixed>
     */
    public function performAction(BranchKiosk $kiosk, string $pin, string $action, ?string $breakType = null, bool $rotaOverride = false, ?string $idempotencyKey = null): array
    {
        $staff = $this->requireStaffByPin($kiosk, $pin);
        $key = $idempotencyKey ?? Str::uuid()->toString();

        return match ($action) {
            'clock-out' => array_merge($this->clockOut($kiosk, $staff, $key), ['step' => 'success']),
            'start-break' => array_merge($this->startBreak($kiosk, $staff, (string) $breakType, $key), ['step' => 'success']),
            'end-break' => array_merge($this->endBreak($kiosk, $staff, $key), ['step' => 'success']),
            'clock-in-override' => array_merge($this->clockIn($kiosk, $staff, $key, true), ['step' => 'success']),
            default => throw ValidationException::withMessages(['action' => 'Unknown action.']),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function clockIn(BranchKiosk $kiosk, User $staff, ?string $idempotencyKey = null, bool $rotaOverride = false): array
    {
        $settings = $this->config->forKiosk($kiosk);
        if (! ($settings['allow_clock_in'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Clock-in is disabled on this kiosk.']);
        }

        $state = $this->resolveState($staff);
        if ($state['state'] !== 'not_checked_in') {
            throw ValidationException::withMessages(['action' => 'Already clocked in.']);
        }

        $rota = $this->rota->evaluateClockIn($staff, $kiosk, $this->config);
        if (! $rota['allowed'] && ! $rotaOverride) {
            throw ValidationException::withMessages(['action' => $rota['message'] ?? 'Clock-in is not allowed right now.']);
        }

        if ($idempotencyKey !== null) {
            $existing = $this->findExistingLog($idempotencyKey);
            if ($existing !== null) {
                return $this->successPayload($staff, 'clock-in', 'Clocked In');
            }
        }

        $this->attendance->createLog([
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'user_id' => $staff->id,
            'branch_kiosk_id' => $kiosk->id,
            'type' => AttendanceLogType::ClockIn->value,
            'source' => AttendanceSource::Kiosk->value,
            'logged_at' => now(),
            'idempotency_key' => $idempotencyKey,
        ]);

        return $this->successPayload($staff, 'clock-in', 'Clocked In', [
            'rota_override' => $rotaOverride,
            'rota_warning' => $rota['message'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function clockOut(BranchKiosk $kiosk, User $staff, ?string $idempotencyKey = null): array
    {
        $settings = $this->config->forKiosk($kiosk);
        if (! ($settings['allow_clock_out'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Clock-out is disabled on this kiosk.']);
        }

        $state = $this->resolveState($staff);
        if (! in_array($state['state'], ['checked_in', 'on_break'], true)) {
            throw ValidationException::withMessages(['action' => 'Not currently clocked in.']);
        }

        if ($idempotencyKey !== null) {
            $existing = $this->findExistingLog($idempotencyKey);
            if ($existing !== null) {
                return $this->successPayload($staff, 'clock-out', 'Clocked Out', ['hours' => $this->hoursWorkedToday($staff)]);
            }
        }

        if ($state['state'] === 'on_break' && $state['break'] !== null) {
            $this->breaks->end($state['break']);
        }

        $this->attendance->createLog([
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'user_id' => $staff->id,
            'branch_kiosk_id' => $kiosk->id,
            'type' => AttendanceLogType::ClockOut->value,
            'source' => AttendanceSource::Kiosk->value,
            'logged_at' => now(),
            'idempotency_key' => $idempotencyKey,
        ]);

        return $this->successPayload($staff, 'clock-out', 'Clocked Out', [
            'hours' => $this->hoursWorkedToday($staff),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function startBreak(BranchKiosk $kiosk, User $staff, string $breakType, ?string $idempotencyKey = null): array
    {
        $settings = $this->config->forKiosk($kiosk);
        if (! ($settings['allow_breaks'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'Breaks are disabled on this kiosk.']);
        }

        $type = BreakType::tryFrom($breakType) ?? BreakType::Other;
        $typeConfig = (array) data_get($settings, 'break_types.'.$type->value, $type->defaultConfig());

        if (! ($typeConfig['enabled'] ?? true)) {
            throw ValidationException::withMessages(['action' => 'This break type is not enabled.']);
        }

        $state = $this->resolveState($staff);
        if ($state['state'] !== 'checked_in') {
            throw ValidationException::withMessages(['action' => 'Must be clocked in to start a break.']);
        }

        if (! ($typeConfig['allow_multiple'] ?? false)) {
            $alreadyTaken = AttendanceBreak::query()
                ->where('user_id', $staff->id)
                ->whereDate('break_started_at', now()->toDateString())
                ->where('break_type', $type->value)
                ->exists();

            if ($alreadyTaken) {
                throw ValidationException::withMessages(['action' => 'This break type was already used today.']);
            }
        }

        $break = $this->breaks->start(
            kiosk: $kiosk,
            staff: $staff,
            type: $type,
            config: $typeConfig,
            idempotencyKey: $idempotencyKey,
        );

        return $this->successPayload($staff, 'start-break', $type->label().' Break Started', [
            'break' => $this->breakPayload($break),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function endBreak(BranchKiosk $kiosk, User $staff, ?string $idempotencyKey = null): array
    {
        $state = $this->resolveState($staff);
        if ($state['state'] !== 'on_break' || $state['break'] === null) {
            throw ValidationException::withMessages(['action' => 'No active break.']);
        }

        $break = $this->breaks->end($state['break']);
        $type = $this->resolveBreakType($break->break_type);
        $label = $type->label();

        return $this->successPayload($staff, 'end-break', $label.' Break Ended', [
            'break' => $this->breakPayload($break),
        ]);
    }

    /**
     * @return array{user: User, state: string, break: ?AttendanceBreak}
     */
    public function resolveState(User $staff): array
    {
        $logs = $this->attendance->logsForUserOnDate($staff->id, now());
        $last = $logs->last();
        $break = $this->breaks->activeBreak($staff);

        if ($break !== null) {
            return ['user' => $staff, 'state' => 'on_break', 'break' => $break];
        }

        if ($last === null || $last->type === AttendanceLogType::ClockOut) {
            return ['user' => $staff, 'state' => 'not_checked_in', 'break' => null];
        }

        return ['user' => $staff, 'state' => 'checked_in', 'break' => null];
    }

    private function requireStaffByContext(KioskContext $context, string $pin): User
    {
        $staff = $this->staff->findByPin($context->organizationId, $pin);

        if ($staff === null || ! StaffPinHasher::verify($pin, (string) $staff->pin_hash)) {
            throw ValidationException::withMessages(['pin' => 'Invalid PIN.']);
        }

        if ((int) $staff->branch_id !== (int) $context->branchId) {
            throw ValidationException::withMessages(['pin' => 'Invalid PIN.']);
        }

        return $staff;
    }

    private function requireStaffByPin(BranchKiosk $kiosk, string $pin): User
    {
        $staff = $this->staff->findByPin((int) $kiosk->organization_id, $pin);

        if ($staff === null || ! StaffPinHasher::verify($pin, (string) $staff->pin_hash)) {
            throw ValidationException::withMessages(['pin' => 'PIN not recognized. Try again.']);
        }

        if ((int) $staff->branch_id !== (int) $kiosk->branch_id) {
            throw ValidationException::withMessages(['pin' => 'PIN not recognized. Try again.']);
        }

        return $staff;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array{action: string, label: string, break_type?: string}>
     */
    private function actionsForCheckedIn(BranchKiosk $kiosk, array $settings): array
    {
        $actions = [];

        foreach ($this->config->enabledBreakTypes($kiosk) as $type) {
            $actions[] = [
                'action' => 'start-break',
                'label' => $type['label'],
                'break_type' => $type['value'],
            ];
        }

        if ($settings['allow_clock_out'] ?? true) {
            $actions[] = ['action' => 'clock-out', 'label' => 'Clock Out'];
        }

        return $actions;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array{action: string, label: string}>
     */
    private function actionsForOnBreak(BranchKiosk $kiosk, array $settings): array
    {
        return [
            ['action' => 'end-break', 'label' => 'Return to Work'],
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function successPayload(User $staff, string $action, string $label, array $extra = []): array
    {
        return array_merge([
            'ok' => true,
            'action' => $action,
            'action_label' => $label,
            'user' => $this->staffPayload($staff),
            'time' => now()->format('g:i A'),
        ], $extra);
    }

    /**
     * @return array{id: int, name: string, avatar_url: ?string}
     */
    private function staffPayload(User $staff): array
    {
        return [
            'id' => $staff->id,
            'name' => $staff->name,
            'avatar_url' => $staff->avatar_path ? \Illuminate\Support\Facades\Storage::url($staff->avatar_path) : null,
        ];
    }

    public function staffForPin(BranchKiosk $kiosk, string $pin): User
    {
        return $this->requireStaffByPin($kiosk, $pin);
    }

    /**
     * @return array{break_type: string, label: string, started_at: string}|null
     */
    private function breakPayload(?AttendanceBreak $break): ?array
    {
        if ($break === null) {
            return null;
        }

        $type = $this->resolveBreakType($break->break_type);

        return [
            'break_type' => $type->value,
            'label' => $type->label(),
            'started_at' => $break->break_started_at?->format('g:i A') ?? '',
        ];
    }

    private function findExistingLog(string $idempotencyKey): ?AttendanceLog
    {
        return AttendanceLog::query()->where('idempotency_key', $idempotencyKey)->first();
    }

    private function resolveBreakType(mixed $breakType): BreakType
    {
        if ($breakType instanceof BreakType) {
            return $breakType;
        }

        return BreakType::tryFrom((string) ($breakType ?? 'other')) ?? BreakType::Other;
    }

    private function hoursWorkedToday(User $staff): float
    {
        $logs = $this->attendance->logsForUserOnDate($staff->id, now());
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

    private function breakLabelFor(?AttendanceBreak $break): string
    {
        if ($break === null) {
            return 'Break';
        }

        if ($break->kiosk_break_type_id) {
            $type = KioskBreakType::query()->find($break->kiosk_break_type_id);
            if ($type) {
                return $type->name;
            }
        }

        return $this->resolveBreakType($break->break_type)->label();
    }

    private function netWorkingMinutesToday(User $staff): int
    {
        $logs = $this->attendance->logsForUserOnDate($staff->id, now());
        $grossSeconds = 0;
        $in = null;

        foreach ($logs as $log) {
            if ($log->type === AttendanceLogType::ClockIn) {
                $in = $log->logged_at;
            } elseif ($log->type === AttendanceLogType::ClockOut && $in !== null) {
                $grossSeconds += $in->diffInSeconds($log->logged_at);
                $in = null;
            }
        }

        $unpaidBreakSeconds = 0;
        $breaks = AttendanceBreak::query()
            ->where('user_id', $staff->id)
            ->whereDate('break_started_at', now()->toDateString())
            ->whereNotNull('break_ended_at')
            ->get();

        foreach ($breaks as $break) {
            if (! $break->is_paid && $break->break_started_at && $break->break_ended_at) {
                $unpaidBreakSeconds += $break->break_started_at->diffInSeconds($break->break_ended_at);
            }
        }

        return max(0, (int) round(($grossSeconds - $unpaidBreakSeconds) / 60));
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }

        return "{$mins}m";
    }
}
