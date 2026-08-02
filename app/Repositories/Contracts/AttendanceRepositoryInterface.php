<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface AttendanceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection<int, AttendanceLog>
     */
    public function logsForUserOnDate(int $userId, Carbon $date): Collection;

    public function createLog(array $attributes): AttendanceLog;

    public function activeBreak(int $userId): ?AttendanceBreak;

    public function createBreak(array $attributes): AttendanceBreak;

    /**
     * @return Collection<int, AttendanceLog>
     */
    public function logsForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection;

    public function deleteUserLogsOnDate(int $userId, Carbon $date): void;

    public function clockedInCountToday(int $organizationId, ?int $branchId): int;

    /**
     * @return Collection<int, AttendanceLog>
     */
    public function getLogsForUserDate(int $userId, string $date): Collection;

    /**
     * @return Collection<int, AttendanceLog>
     */
    public function getLogsForBranchDate(int $organizationId, int $branchId, string $date): Collection;

    public function getLatestLogForUser(int $userId): ?AttendanceLog;

    public function getActiveBreak(int $userId): ?AttendanceBreak;

    public function startBreak(array $data): AttendanceBreak;

    public function endBreak(int $breakId, string $endedAt): AttendanceBreak;
}
