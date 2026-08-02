<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\RotaGroup;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Models\User;
use App\Repositories\Contracts\RotaRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RotaService implements ServiceInterface
{
    public function __construct(
        private readonly RotaRepositoryInterface $rota,
        private readonly StaffRepositoryInterface $staff,
        private readonly BranchContext $branchContext,
    ) {}

    public function weekView(User $user, string $weekStart): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from = Carbon::parse($weekStart)->startOfWeek();
        $to = $from->copy()->endOfWeek();

        $shifts = RotaShift::query()
            ->with(['user', 'rotaSection'])
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('shift_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();

        $staffList = $this->staff->activeStaff($orgId, $branchId);
        $sections = RotaSection::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        $groups = RotaGroup::query()
            ->with('users')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $days = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $days[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('D j'),
                'full' => $day->format('D, M j'),
            ];
        }

        return [
            'days' => $days,
            'staff' => $staffList->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
            ])->values(),
            'sections' => $sections->map(fn (RotaSection $section) => [
                'id' => $section->id,
                'name' => $section->name,
                'color' => $section->color,
            ])->values(),
            'groups' => $groups->map(fn (RotaGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'color' => $group->color,
                'display_order' => $group->display_order,
                'users' => $group->users->map(fn (User $member) => [
                    'id' => $member->id,
                    'name' => $member->name,
                ])->values(),
            ])->values(),
            'morningGrid' => $this->buildGrid($staffList, $days, $shifts, 'Morning'),
            'eveningGrid' => $this->buildGrid($staffList, $days, $shifts, 'Evening'),
            'from' => $from,
            'to' => $to,
            'weekStart' => $from->toDateString(),
            'weekLabel' => 'Week of '.$from->format('d M Y'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeShift(User $user, array $data): RotaShift
    {
        $staff = User::query()->findOrFail((int) $data['user_id']);

        if ((int) $staff->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $section = RotaSection::query()->findOrFail((int) $data['rota_section_id']);
        if ((int) $section->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $branchId = (int) ($staff->branch_id ?: $section->branch_id ?: $this->branchContext->requireBranchId($user));
        $date = Carbon::parse($data['shift_date'])->toDateString();
        $start = Carbon::parse($date.' '.$data['start_time']);
        $end = Carbon::parse($date.' '.$data['end_time']);
        if ($end->lte($start)) {
            $end->addDay();
        }

        $this->assertNoOverlap((int) $staff->id, $start, $end, isset($data['id']) ? (int) $data['id'] : null);

        $payload = [
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => $date,
            'start_time' => $start,
            'end_time' => $end,
            'shift_type' => $data['shift_type'] ?? 'Morning',
        ];

        if (! empty($data['id'])) {
            $shift = RotaShift::query()->findOrFail((int) $data['id']);
            if ((int) $shift->organization_id !== (int) $user->organization_id) {
                abort(403);
            }
            $shift->update($payload);

            return $shift->refresh();
        }

        return RotaShift::query()->create($payload);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeSection(User $user, array $data): RotaSection
    {
        return RotaSection::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $this->branchContext->requireBranchId($user),
            'name' => $data['name'],
            'color' => $data['color'] ?? '#563d7c',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeGroup(User $user, array $data): RotaGroup
    {
        return DB::transaction(function () use ($user, $data): RotaGroup {
            $group = RotaGroup::query()->create([
                'organization_id' => $user->organization_id,
                'branch_id' => $this->branchContext->requireBranchId($user),
                'name' => $data['name'],
                'color' => $data['color'] ?? '#007bff',
                'display_order' => (int) ($data['display_order'] ?? 0),
            ]);

            $userIds = collect($data['user_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();
            if ($userIds->isNotEmpty()) {
                DB::table('rota_group_user')->whereIn('user_id', $userIds)->delete();
                $group->users()->sync($userIds->all());
            }

            return $group->load('users');
        });
    }

    public function destroyShift(User $user, int $shiftId): void
    {
        $shift = RotaShift::query()->findOrFail($shiftId);

        if ((int) $shift->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $shift->delete();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, User>  $staffList
     * @param  list<array{date: string, label: string}>  $days
     * @param  \Illuminate\Support\Collection<int, RotaShift>  $shifts
     * @return list<array{user_id: int, name: string, cells: list<array<string, mixed>|null>, total_days: int, total_hours: float}>
     */
    private function buildGrid($staffList, array $days, $shifts, string $type): array
    {
        return $staffList->map(function (User $member) use ($days, $shifts, $type): array {
            $cells = [];
            $totalDays = 0;
            $totalHours = 0.0;

            foreach ($days as $day) {
                $shift = $shifts->first(function (RotaShift $item) use ($member, $day, $type): bool {
                    return (int) $item->user_id === (int) $member->id
                        && $item->shift_date?->toDateString() === $day['date']
                        && $item->shift_type === $type;
                });

                if ($shift) {
                    $hours = max(0, $shift->start_time->diffInMinutes($shift->end_time) / 60);
                    $totalDays++;
                    $totalHours += $hours;
                    $cells[] = [
                        'id' => $shift->id,
                        'rota_section_id' => $shift->rota_section_id,
                        'start_time' => $shift->start_time?->format('H:i'),
                        'end_time' => $shift->end_time?->format('H:i'),
                        'section' => $shift->rotaSection?->name,
                        'color' => $shift->rotaSection?->color,
                    ];
                } else {
                    $cells[] = null;
                }
            }

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'cells' => $cells,
                'total_days' => $totalDays,
                'total_hours' => round($totalHours, 1),
            ];
        })->values()->all();
    }

    private function assertNoOverlap(int $userId, Carbon $start, Carbon $end, ?int $ignoreId = null): void
    {
        $overlap = RotaShift::query()
            ->where('user_id', $userId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->first();

        if ($overlap !== null) {
            throw ValidationException::withMessages([
                'shift' => sprintf(
                    'This overlaps an existing shift ending %s on %s. Please change either shift.',
                    $overlap->end_time?->format('H:i'),
                    $overlap->shift_date?->format('d M Y'),
                ),
            ]);
        }
    }
}
