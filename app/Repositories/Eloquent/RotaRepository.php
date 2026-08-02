<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\RotaGroup;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Repositories\Contracts\RotaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

final class RotaRepository extends BaseRepository implements RotaRepositoryInterface
{
    public function __construct(RotaShift $model)
    {
        parent::__construct($model);
    }

    public function getGroupsForBranch(int $organizationId, int $branchId): Collection
    {
        return RotaGroup::query()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    public function getSectionsForBranch(int $organizationId, int $branchId): Collection
    {
        return RotaSection::query()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();
    }

    public function getShiftsForDateRange(int $organizationId, int $branchId, string $startDate, string $endDate): Collection
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->with(['user', 'rotaSection', 'section', 'rotaGroup', 'branch'])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    public function getShiftsForUser(int $userId, string $startDate, string $endDate): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->with(['rotaSection', 'rotaGroup'])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    public function createGroup(array $data): RotaGroup
    {
        return RotaGroup::query()->create($data);
    }

    public function createSection(array $data): RotaSection
    {
        return RotaSection::query()->create($data);
    }

    public function createShift(array $data): RotaShift
    {
        return $this->model->newQuery()->create($data);
    }

    public function deleteShift(int $shiftId): bool
    {
        $shift = $this->model->newQuery()->findOrFail($shiftId);

        return (bool) $shift->delete();
    }

    public function shiftsForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection
    {
        $branch = $branchId ?? 0;

        if ($branch === 0) {
            return $this->model->newQuery()
                ->where('organization_id', $organizationId)
                ->whereBetween('shift_date', [$from->toDateString(), $to->toDateString()])
                ->with(['user', 'section', 'group', 'branch'])
                ->orderBy('start_time')
                ->get();
        }

        return $this->getShiftsForDateRange(
            $organizationId,
            $branch,
            $from->toDateString(),
            $to->toDateString(),
        );
    }

    public function sections(int $organizationId, int $branchId): Collection
    {
        return $this->getSectionsForBranch($organizationId, $branchId);
    }

    public function groups(int $organizationId, int $branchId): Collection
    {
        return $this->getGroupsForBranch($organizationId, $branchId);
    }
}
