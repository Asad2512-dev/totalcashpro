<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\SavedReport;
use App\Models\User;
use App\Support\Reports\ReportCenterFilter;
use Illuminate\Support\Collection;

final class SavedReportService implements ServiceInterface
{
    /**
     * @return Collection<int, SavedReport>
     */
    public function list(User $user): Collection
    {
        return SavedReport::query()
            ->where('organization_id', $user->organization_id)
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function save(User $user, ReportCenterFilter $filter, string $name): SavedReport
    {
        return SavedReport::query()->create([
            'organization_id' => $user->organization_id,
            'user_id' => $user->id,
            'name' => $name,
            'report_type' => $filter->reportType->value,
            'filters' => [
                'from' => $filter->from,
                'to' => $filter->to,
                'branch_id' => $filter->branchId,
                'compare' => $filter->compareMode->value,
            ],
        ]);
    }
}
