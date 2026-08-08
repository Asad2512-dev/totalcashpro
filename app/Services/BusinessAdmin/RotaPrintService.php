<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RotaVersionStatus;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class RotaPrintService implements ServiceInterface
{
    public function __construct(
        private readonly RotaVersionService $versions,
        private readonly BranchContext $branchContext,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function weekPrintData(User $admin, RotaVersion $version, ?string $sectionFilter = null, ?int $groupFilter = null, ?int $staffFilter = null): array
    {
        if ((int) $version->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        $version->load(['branch', 'organization', 'publishedBy']);
        $from = $version->week_start->copy();
        $to = $from->copy()->endOfWeek();

        $days = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $days[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('D'),
                'full' => $day->format('D j M'),
            ];
        }

        $query = $version->shifts()
            ->with(['user', 'rotaSection', 'rotaGroup'])
            ->orderBy('user_id')
            ->orderBy('shift_date')
            ->orderBy('start_time');

        if ($sectionFilter) {
            $query->whereHas('rotaSection', fn ($q) => $q->where('name', $sectionFilter));
        }

        if ($groupFilter) {
            $query->where('rota_group_id', $groupFilter);
        }

        if ($staffFilter) {
            $query->where('user_id', $staffFilter);
        }

        $shifts = $query->get();
        $staffIds = $shifts->pluck('user_id')->unique();

        $rows = $staffIds->map(function (int $userId) use ($shifts, $days): array {
            $userShifts = $shifts->where('user_id', $userId);
            $user = $userShifts->first()?->user;
            $cells = [];
            $totalHours = 0.0;
            $shiftCount = 0;

            foreach ($days as $day) {
                $dayShifts = $userShifts->filter(fn (RotaShift $s) => $s->shift_date?->toDateString() === $day['date']);
                if ($dayShifts->isEmpty()) {
                    $cells[] = null;
                } else {
                    $cellShifts = $dayShifts->map(function (RotaShift $shift) use (&$totalHours, &$shiftCount): array {
                        $hours = max(0, $shift->start_time->diffInMinutes($shift->end_time) / 60);
                        $totalHours += $hours;
                        $shiftCount++;

                        return [
                            'time' => $shift->start_time->format('H:i').' – '.$shift->end_time->format('H:i'),
                            'section' => $shift->rotaSection?->name,
                            'type' => $shift->shift_type,
                        ];
                    })->values()->all();
                    $cells[] = $cellShifts;
                }
            }

            return [
                'name' => $user?->name ?? 'Staff',
                'cells' => $cells,
                'total_hours' => round($totalHours, 1),
                'shift_count' => $shiftCount,
            ];
        })->values()->all();

        $totalHours = round($shifts->sum(fn (RotaShift $s) => max(0, $s->start_time->diffInMinutes($s->end_time) / 60)), 1);

        return [
            'version' => $version,
            'organization' => $version->organization,
            'branch' => $version->branch,
            'days' => $days,
            'rows' => $rows,
            'weekLabel' => $version->weekLabel(),
            'generatedAt' => now(),
            'preparedBy' => $admin->name,
            'summary' => [
                'staff_count' => $staffIds->count(),
                'shift_count' => $shifts->count(),
                'total_hours' => $totalHours,
            ],
            'statusLabel' => strtoupper($version->status->label()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function staffPrintData(User $staff, string $weekStart): array
    {
        $week = Carbon::parse($weekStart)->startOfWeek();
        $version = $this->versions->staffPublishedVersion(
            (int) $staff->organization_id,
            (int) $staff->branch_id,
            $week,
        );

        if ($version === null) {
            return [
                'staff' => $staff,
                'weekLabel' => $week->format('d M').' – '.$week->copy()->endOfWeek()->format('d M Y'),
                'days' => [],
                'shifts' => collect(),
                'summary' => ['total_hours' => 0, 'shift_count' => 0],
            ];
        }

        $shifts = $version->shifts()
            ->with(['rotaSection', 'rotaGroup', 'branch'])
            ->where('user_id', $staff->id)
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();

        $days = [];
        for ($day = $week->copy(); $day->lte($week->copy()->endOfWeek()); $day->addDay()) {
            $dayShifts = $shifts->filter(fn (RotaShift $s) => $s->shift_date?->isSameDay($day));
            $days[] = [
                'label' => $day->format('l'),
                'date' => $day->format('j M'),
                'off' => $dayShifts->isEmpty(),
                'shifts' => $dayShifts->map(fn (RotaShift $s) => [
                    'time' => $s->start_time->format('H:i').' – '.$s->end_time->format('H:i'),
                    'section' => $s->rotaSection?->name,
                    'group' => $s->rotaGroup?->name,
                    'branch' => $s->branch?->name,
                    'break' => $s->break_minutes,
                    'hours' => round(max(0, $s->start_time->diffInMinutes($s->end_time) / 60), 1),
                ])->values()->all(),
            ];
        }

        $totalHours = round($shifts->sum(fn (RotaShift $s) => max(0, $s->start_time->diffInMinutes($s->end_time) / 60)), 1);

        return [
            'staff' => $staff,
            'version' => $version,
            'weekLabel' => $version->weekLabel(),
            'days' => $days,
            'shifts' => $shifts,
            'summary' => [
                'total_hours' => $totalHours,
                'shift_count' => $shifts->count(),
            ],
            'generatedAt' => now(),
        ];
    }

    public function resolvePrintVersion(User $admin, string $weekStart): ?RotaVersion
    {
        $orgId = (int) $admin->organization_id;
        $branchId = $this->branchContext->requireBranchId($admin);
        $week = Carbon::parse($weekStart)->startOfWeek();

        return RotaVersion::query()
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $week)
            ->whereIn('status', [RotaVersionStatus::Published, RotaVersionStatus::Locked, RotaVersionStatus::Finalized, RotaVersionStatus::Draft])
            ->orderByRaw("FIELD(status, 'published', 'locked', 'finalized', 'draft')")
            ->orderByDesc('version_number')
            ->first();
    }
}
