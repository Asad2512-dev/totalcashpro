<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\RotaGroup;
use App\Models\RotaSection;
use App\Models\RotaShift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface RotaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all groups for a branch.
     */
    public function getGroupsForBranch(int $organizationId, int $branchId): Collection;

    /**
     * Get all sections for a branch.
     */
    public function getSectionsForBranch(int $organizationId, int $branchId): Collection;

    /**
     * Get shifts for a date range.
     */
    public function getShiftsForDateRange(int $organizationId, int $branchId, string $startDate, string $endDate): Collection;

    /**
     * Get shifts for a user.
     */
    public function getShiftsForUser(int $userId, string $startDate, string $endDate): Collection;

    /**
     * Create a group.
     *
     * @param  array<string, mixed>  $data
     */
    public function createGroup(array $data): RotaGroup;

    /**
     * Create a section.
     *
     * @param  array<string, mixed>  $data
     */
    public function createSection(array $data): RotaSection;

    /**
     * Create a shift.
     *
     * @param  array<string, mixed>  $data
     */
    public function createShift(array $data): RotaShift;

    /**
     * Delete a shift.
     */
    public function deleteShift(int $shiftId): bool;

    /**
     * @return Collection<int, RotaShift>
     */
    public function shiftsForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection;

    /**
     * @return Collection<int, RotaSection>
     */
    public function sections(int $organizationId, int $branchId): Collection;

    /**
     * @return Collection<int, RotaGroup>
     */
    public function groups(int $organizationId, int $branchId): Collection;
}
