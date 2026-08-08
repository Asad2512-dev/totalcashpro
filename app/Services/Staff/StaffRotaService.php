<?php

declare(strict_types=1);

namespace App\Services\Staff;

use App\Contracts\ServiceInterface;
use App\Models\RotaShift;
use App\Models\User;
use App\Services\BusinessAdmin\AttendanceService;
use App\Services\BusinessAdmin\RotaVersionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class StaffRotaService implements ServiceInterface
{
    public function __construct(
        private readonly RotaVersionService $versions,
        private readonly AttendanceService $attendance,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function weekView(User $staff, string $weekStart): array
    {
        $week = Carbon::parse($weekStart)->startOfWeek();
        $weekEnd = $week->copy()->endOfWeek();

        $version = $this->versions->staffPublishedVersion(
            (int) $staff->organization_id,
            (int) $staff->branch_id,
            $week,
        );

        $shifts = $version
            ? $version->shifts()
                ->with(['rotaSection', 'rotaGroup', 'branch'])
                ->where('user_id', $staff->id)
                ->whereBetween('shift_date', [$week->toDateString(), $weekEnd->toDateString()])
                ->orderBy('shift_date')
                ->orderBy('start_time')
                ->get()
            : collect();

        $days = [];
        for ($day = $week->copy(); $day->lte($weekEnd); $day->addDay()) {
            $dayShifts = $shifts->filter(fn (RotaShift $s) => $s->shift_date?->isSameDay($day));
            $dayHours = $dayShifts->sum(fn (RotaShift $s) => max(0, $s->start_time->diffInMinutes($s->end_time) / 60));

            $days[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('l'),
                'short' => $day->format('D j M'),
                'is_today' => $day->isToday(),
                'is_tomorrow' => $day->isTomorrow(),
                'off' => $dayShifts->isEmpty(),
                'shifts' => $dayShifts->map(fn (RotaShift $s) => $this->formatShift($s))->values()->all(),
                'scheduled_hours' => round($dayHours, 1),
            ];
        }

        $todayShift = $shifts->first(fn (RotaShift $s) => $s->shift_date?->isSameDay(now()));
        $tomorrowShift = $shifts->first(fn (RotaShift $s) => $s->shift_date?->isSameDay(now()->addDay()));
        $attendanceState = $this->attendance->currentStateForStaff($staff);
        $scheduledHours = round($shifts->sum(fn (RotaShift $s) => max(0, $s->start_time->diffInMinutes($s->end_time) / 60)), 1);

        return [
            'weekStart' => $week->toDateString(),
            'weekLabel' => $week->format('d M').' – '.$weekEnd->format('d M Y'),
            'days' => $days,
            'shifts' => $shifts,
            'todayShift' => $todayShift ? $this->formatShift($todayShift) : null,
            'tomorrowShift' => $tomorrowShift ? $this->formatShift($tomorrowShift) : null,
            'attendanceState' => $attendanceState,
            'summary' => [
                'scheduled_hours' => $scheduledHours,
                'shift_count' => $shifts->count(),
                'branch' => $staff->branch?->name,
            ],
            'publishedVersion' => $version,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatShift(RotaShift $shift): array
    {
        $hours = max(0, $shift->start_time->diffInMinutes($shift->end_time) / 60);

        return [
            'id' => $shift->id,
            'date' => $shift->shift_date?->toDateString(),
            'day_label' => $shift->shift_date?->format('l'),
            'date_label' => $shift->shift_date?->format('D j M'),
            'type' => $shift->shift_type,
            'start' => $shift->start_time?->format('H:i'),
            'end' => $shift->end_time?->format('H:i'),
            'time' => $shift->start_time?->format('H:i').' – '.$shift->end_time?->format('H:i'),
            'section' => $shift->rotaSection?->name,
            'group' => $shift->rotaGroup?->name,
            'branch' => $shift->branch?->name,
            'break_minutes' => $shift->break_minutes,
            'hours' => round($hours, 1),
            'status' => 'Scheduled',
        ];
    }
}
