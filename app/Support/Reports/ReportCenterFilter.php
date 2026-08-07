<?php

declare(strict_types=1);

namespace App\Support\Reports;

use App\Enums\ReportCompareMode;
use App\Enums\ReportDatePreset;
use App\Enums\ReportType;
use Illuminate\Http\Request;

final class ReportCenterFilter
{
    public function __construct(
        public readonly ReportDatePreset $datePreset,
        public readonly string $from,
        public readonly string $to,
        public readonly ?int $branchId,
        public readonly ReportType $reportType,
        public readonly ?int $employeeId,
        public readonly ?string $status,
        public readonly ReportCompareMode $compareMode,
        public readonly ?int $compareBranchId,
        public readonly ?int $compareEmployeeId,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $preset = ReportDatePreset::tryFrom($request->string('date_preset', 'last_30_days')->toString())
            ?? ReportDatePreset::Last30Days;

        $range = ReportDateRangeResolver::resolve(
            $preset,
            $request->input('from'),
            $request->input('to'),
        );

        $reportType = ReportType::tryFrom($request->string('report_type', 'overview')->toString())
            ?? ReportType::Overview;

        $compareMode = ReportCompareMode::tryFrom($request->string('compare', 'none')->toString())
            ?? ReportCompareMode::None;

        $branchRaw = $request->input('branch_id');
        $branchId = ($branchRaw === null || $branchRaw === '' || $branchRaw === 'all') ? null : (int) $branchRaw;

        $employeeRaw = $request->input('employee_id');
        $employeeId = ($employeeRaw === null || $employeeRaw === '' || $employeeRaw === 'all') ? null : (int) $employeeRaw;

        $statusRaw = $request->input('status');
        $status = ($statusRaw === null || $statusRaw === '' || $statusRaw === 'all') ? null : (string) $statusRaw;

        $compareBranchRaw = $request->input('compare_branch_id');
        $compareBranchId = ($compareBranchRaw === null || $compareBranchRaw === '') ? null : (int) $compareBranchRaw;

        $compareEmployeeRaw = $request->input('compare_employee_id');
        $compareEmployeeId = ($compareEmployeeRaw === null || $compareEmployeeRaw === '') ? null : (int) $compareEmployeeRaw;

        return new self(
            datePreset: $preset,
            from: $range['from'],
            to: $range['to'],
            branchId: $branchId,
            reportType: $reportType,
            employeeId: $employeeId,
            status: $status,
            compareMode: $compareMode,
            compareBranchId: $compareBranchId,
            compareEmployeeId: $compareEmployeeId,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toQueryArray(): array
    {
        return [
            'date_preset' => $this->datePreset->value,
            'from' => $this->from,
            'to' => $this->to,
            'branch_id' => $this->branchId ?? 'all',
            'report_type' => $this->reportType->value,
            'employee_id' => $this->employeeId ?? 'all',
            'status' => $this->status ?? 'all',
            'compare' => $this->compareMode->value,
            'compare_branch_id' => $this->compareBranchId,
            'compare_employee_id' => $this->compareEmployeeId,
        ];
    }
}
