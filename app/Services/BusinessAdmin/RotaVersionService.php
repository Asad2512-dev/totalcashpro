<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RotaVersionStatus;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\User;
use App\Repositories\Contracts\RotaRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class RotaVersionService implements ServiceInterface
{
    public function __construct(
        private readonly RotaRepositoryInterface $rota,
        private readonly BranchContext $branchContext,
    ) {}

    public function resolveDraftVersion(User $admin, string $weekStart): RotaVersion
    {
        $orgId = (int) $admin->organization_id;
        $branchId = $this->branchContext->requireBranchId($admin);
        $week = Carbon::parse($weekStart)->startOfWeek();

        $draft = RotaVersion::query()
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $week)
            ->where('status', RotaVersionStatus::Draft)
            ->first();

        if ($draft !== null) {
            return $draft;
        }

        $published = $this->publishedVersion($orgId, $branchId, $week);

        return DB::transaction(function () use ($admin, $orgId, $branchId, $week, $published): RotaVersion {
            $nextNumber = (int) RotaVersion::query()
                ->where('organization_id', $orgId)
                ->where('branch_id', $branchId)
                ->whereDate('week_start', $week)
                ->max('version_number') + 1;

            $version = RotaVersion::query()->create([
                'organization_id' => $orgId,
                'branch_id' => $branchId,
                'week_start' => $week->toDateString(),
                'version_number' => max(1, $nextNumber),
                'status' => RotaVersionStatus::Draft,
                'created_by_user_id' => $admin->id,
            ]);

            if ($published !== null) {
                $this->copyShifts($published, $version);
            }

            return $version;
        });
    }

    public function publishedVersion(int $orgId, int $branchId, Carbon $week): ?RotaVersion
    {
        return RotaVersion::query()
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $week->copy()->startOfWeek())
            ->where('status', RotaVersionStatus::Published)
            ->orderByDesc('version_number')
            ->first();
    }

    public function staffPublishedVersion(int $orgId, int $branchId, Carbon $week): ?RotaVersion
    {
        return RotaVersion::query()
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $week->copy()->startOfWeek())
            ->whereIn('status', [RotaVersionStatus::Published, RotaVersionStatus::Locked])
            ->orderByDesc('version_number')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, RotaVersion>
     */
    public function versionHistory(int $orgId, int $branchId, Carbon $week)
    {
        return RotaVersion::query()
            ->with(['createdBy', 'finalizedBy', 'publishedBy'])
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $week->copy()->startOfWeek())
            ->orderByDesc('version_number')
            ->get();
    }

    public function copyPreviousWeek(User $admin, string $weekStart): RotaVersion
    {
        $orgId = (int) $admin->organization_id;
        $branchId = $this->branchContext->requireBranchId($admin);
        $week = Carbon::parse($weekStart)->startOfWeek();
        $previousWeek = $week->copy()->subWeek();

        $source = RotaVersion::query()
            ->where('organization_id', $orgId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $previousWeek)
            ->whereIn('status', [RotaVersionStatus::Published, RotaVersionStatus::Locked])
            ->orderByDesc('version_number')
            ->first();

        $draft = $this->resolveDraftVersion($admin, $week->toDateString());

        if ($source !== null) {
            DB::transaction(function () use ($source, $draft, $week): void {
                RotaShift::query()->where('rota_version_id', $draft->id)->delete();

                foreach ($source->shifts()->with(['rotaSection'])->get() as $shift) {
                    $dayOffset = $shift->shift_date->diffInDays($source->week_start);
                    $newDate = $week->copy()->addDays($dayOffset);

                    RotaShift::query()->create([
                        'rota_version_id' => $draft->id,
                        'organization_id' => $shift->organization_id,
                        'branch_id' => $shift->branch_id,
                        'user_id' => $shift->user_id,
                        'rota_section_id' => $shift->rota_section_id,
                        'rota_group_id' => $shift->rota_group_id,
                        'shift_date' => $newDate->toDateString(),
                        'start_time' => $newDate->copy()->setTimeFrom($shift->start_time),
                        'end_time' => $newDate->copy()->setTimeFrom($shift->end_time),
                        'shift_type' => $shift->shift_type,
                        'break_minutes' => $shift->break_minutes,
                        'status' => 'published',
                    ]);
                }
            });
        }

        return $draft->refresh();
    }

    public function clearWeek(User $admin, RotaVersion $version): void
    {
        $this->assertEditable($admin, $version);
        $version->shifts()->delete();
    }

    public function assertEditable(User $admin, RotaVersion $version): void
    {
        if ((int) $version->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        if (! $version->status->isEditable()) {
            abort(403, 'This rota version cannot be edited.');
        }
    }

    private function copyShifts(RotaVersion $from, RotaVersion $to): void
    {
        foreach ($from->shifts()->get() as $shift) {
            RotaShift::query()->create([
                'rota_version_id' => $to->id,
                'organization_id' => $shift->organization_id,
                'branch_id' => $shift->branch_id,
                'user_id' => $shift->user_id,
                'rota_section_id' => $shift->rota_section_id,
                'rota_group_id' => $shift->rota_group_id,
                'shift_date' => $shift->shift_date,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'shift_type' => $shift->shift_type,
                'break_minutes' => $shift->break_minutes,
                'status' => 'draft',
            ]);
        }
    }
}
