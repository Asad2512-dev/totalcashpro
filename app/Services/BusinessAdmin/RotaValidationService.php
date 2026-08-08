<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RotaVersionStatus;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\User;
use App\Services\Logging\AuditLogger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RotaValidationService implements ServiceInterface
{
    public function __construct(
        private readonly RotaVersionService $versions,
    ) {}

    /**
     * @return list<array{severity: string, message: string, user?: string, branch?: string, time?: string}>
     */
    public function conflicts(RotaVersion $version): array
    {
        $shifts = $version->shifts()
            ->with(['user.branch', 'branch', 'rotaSection'])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();

        $conflicts = [];

        foreach ($shifts as $shift) {
            $conflicts = array_merge($conflicts, $this->validateShift($shift, $shifts));
        }

        return $conflicts;
    }

    public function assertCanFinalize(RotaVersion $version): void
    {
        $conflicts = array_filter(
            $this->conflicts($version),
            fn (array $item): bool => ($item['severity'] ?? '') === 'error',
        );

        if ($conflicts !== []) {
            throw ValidationException::withMessages([
                'rota' => collect($conflicts)->pluck('message')->implode(' '),
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, RotaShift>  $allShifts
     * @return list<array{severity: string, message: string, user?: string, branch?: string, time?: string}>
     */
    private function validateShift(RotaShift $shift, $allShifts): array
    {
        $issues = [];
        $staff = $shift->user;

        if ($staff === null || $staff->status !== 'active') {
            $issues[] = [
                'severity' => 'error',
                'message' => 'Inactive or missing employee on '.$shift->shift_date?->format('D j M').'.',
                'user' => $staff?->name,
            ];
        } elseif ((int) $staff->organization_id !== (int) $shift->organization_id) {
            $issues[] = [
                'severity' => 'error',
                'message' => $staff->name.' is not in this organization.',
                'user' => $staff->name,
            ];
        } elseif ((int) $staff->branch_id !== (int) $shift->branch_id) {
            $issues[] = [
                'severity' => 'error',
                'message' => $staff->name.' is assigned outside their branch on '.$shift->shift_date?->format('D j M').'.',
                'user' => $staff->name,
                'branch' => $shift->branch?->name,
            ];
        }

        if ($shift->end_time !== null && $shift->start_time !== null && $shift->end_time->lte($shift->start_time)) {
            $issues[] = [
                'severity' => 'error',
                'message' => 'Shift for '.($staff?->name ?? 'staff').' on '.$shift->shift_date?->format('D j M').' has invalid times.',
                'user' => $staff?->name,
                'time' => $shift->start_time->format('H:i').'–'.$shift->end_time->format('H:i'),
            ];
        }

        $duration = $shift->start_time && $shift->end_time
            ? $shift->start_time->diffInMinutes($shift->end_time)
            : 0;

        if ($duration > 16 * 60) {
            $issues[] = [
                'severity' => 'warning',
                'message' => ($staff?->name ?? 'Staff').' has a shift longer than 16 hours on '.$shift->shift_date?->format('D j M').'.',
                'user' => $staff?->name,
            ];
        }

        foreach ($allShifts as $other) {
            if ((int) $other->id === (int) $shift->id || (int) $other->user_id !== (int) $shift->user_id) {
                continue;
            }

            if ($shift->start_time < $other->end_time && $shift->end_time > $other->start_time) {
                $issues[] = [
                    'severity' => 'error',
                    'message' => sprintf(
                        'Schedule conflict: %s has overlapping shifts on %s (%s–%s and %s–%s).',
                        $staff?->name ?? 'Staff',
                        $shift->shift_date?->format('D j M'),
                        $shift->start_time?->format('H:i'),
                        $shift->end_time?->format('H:i'),
                        $other->start_time?->format('H:i'),
                        $other->end_time?->format('H:i'),
                    ),
                    'user' => $staff?->name,
                ];
            }

            if ((int) $other->branch_id !== (int) $shift->branch_id
                && $shift->shift_date?->toDateString() === $other->shift_date?->toDateString()
                && $shift->start_time < $other->end_time
                && $shift->end_time > $other->start_time) {
                $issues[] = [
                    'severity' => 'error',
                    'message' => sprintf(
                        'Schedule conflict: %s is scheduled in %s and %s at the same time.',
                        $staff?->name ?? 'Staff',
                        $shift->branch?->name ?? 'branch',
                        $other->branch?->name ?? 'branch',
                    ),
                    'user' => $staff?->name,
                ];
            }
        }

        return $issues;
    }

    /**
     * @return array{staff_count: int, shift_count: int, total_hours: float, conflicts: list<array<string, mixed>>}
     */
    public function publishSummary(RotaVersion $version): array
    {
        $shifts = $version->shifts()->with('user')->get();
        $hours = $shifts->sum(fn (RotaShift $s) => max(0, $s->start_time->diffInMinutes($s->end_time) / 60));

        return [
            'staff_count' => $shifts->pluck('user_id')->unique()->count(),
            'shift_count' => $shifts->count(),
            'total_hours' => round($hours, 1),
            'conflicts' => $this->conflicts($version),
        ];
    }

    /**
     * @return list<array{user: string, day: string, before: ?string, after: ?string}>
     */
    public function compareVersions(?RotaVersion $before, RotaVersion $after): array
    {
        if ($before === null) {
            return [];
        }

        $changes = [];
        $beforeShifts = $before->shifts()->with('user')->get()->keyBy(fn (RotaShift $s) => $s->user_id.'|'.$s->shift_date?->toDateString().'|'.$s->shift_type);
        $afterShifts = $after->shifts()->with('user')->get()->keyBy(fn (RotaShift $s) => $s->user_id.'|'.$s->shift_date?->toDateString().'|'.$s->shift_type);

        foreach ($afterShifts as $key => $shift) {
            $prev = $beforeShifts->get($key);
            $label = $shift->start_time?->format('H:i').'–'.$shift->end_time?->format('H:i');
            $prevLabel = $prev ? $prev->start_time?->format('H:i').'–'.$prev->end_time?->format('H:i') : 'OFF';

            if ($prev === null || $prevLabel !== $label) {
                $changes[] = [
                    'user' => $shift->user?->name ?? 'Staff',
                    'day' => $shift->shift_date?->format('l'),
                    'before' => $prev ? $prevLabel : 'OFF',
                    'after' => $label,
                ];
            }
        }

        foreach ($beforeShifts as $key => $shift) {
            if (! $afterShifts->has($key)) {
                $changes[] = [
                    'user' => $shift->user?->name ?? 'Staff',
                    'day' => $shift->shift_date?->format('l'),
                    'before' => $shift->start_time?->format('H:i').'–'.$shift->end_time?->format('H:i'),
                    'after' => 'OFF',
                ];
            }
        }

        return $changes;
    }
}
