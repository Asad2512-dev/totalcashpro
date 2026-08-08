<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\AttendanceLogType;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

final class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    public function __construct(AttendanceLog $model)
    {
        parent::__construct($model);
    }

    public function logsForUserOnDate(int $userId, Carbon $date): Collection
    {
        return $this->getLogsForUserDate($userId, $date->toDateString());
    }

    public function createLog(array $attributes): AttendanceLog
    {
        return $this->model->newQuery()->create($attributes);
    }

    public function activeBreak(int $userId): ?AttendanceBreak
    {
        return $this->getActiveBreak($userId);
    }

    public function createBreak(array $attributes): AttendanceBreak
    {
        return $this->startBreak($attributes);
    }

    public function logsForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection
    {
        return $this->model->newQuery()
            ->with(['user', 'branch', 'branchKiosk'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('logged_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderBy('logged_at')
            ->get();
    }

    public function deleteUserLogsOnDate(int $userId, Carbon $date): void
    {
        $this->model->newQuery()
            ->where('user_id', $userId)
            ->whereDate('logged_at', $date->toDateString())
            ->delete();
    }

    public function clockedInCountToday(int $organizationId, ?int $branchId): int
    {
        $today = now()->toDateString();

        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('logged_at', $today)
            ->orderBy('logged_at')
            ->get()
            ->groupBy('user_id')
            ->filter(function (Collection $logs): bool {
                $last = $logs->last();

                return $last !== null && $last->type === AttendanceLogType::ClockIn;
            })
            ->count();
    }

    public function getLogsForUserDate(int $userId, string $date): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->whereDate('logged_at', $date)
            ->orderBy('logged_at')
            ->get();
    }

    public function getLogsForBranchDate(int $organizationId, int $branchId, string $date): Collection
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->whereDate('logged_at', $date)
            ->with('user')
            ->orderBy('logged_at')
            ->get();
    }

    public function getLatestLogForUser(int $userId): ?AttendanceLog
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->orderByDesc('logged_at')
            ->first();
    }

    public function getActiveBreak(int $userId): ?AttendanceBreak
    {
        return AttendanceBreak::query()
            ->where('user_id', $userId)
            ->whereDate('break_started_at', now()->toDateString())
            ->whereNull('break_ended_at')
            ->latest('break_started_at')
            ->first();
    }

    public function startBreak(array $data): AttendanceBreak
    {
        return AttendanceBreak::query()->create($data);
    }

    public function endBreak(int $breakId, string $endedAt): AttendanceBreak
    {
        $break = AttendanceBreak::query()->findOrFail($breakId);
        $break->update(['break_ended_at' => $endedAt]);

        return $break->refresh();
    }
}
