<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Enums\ReportDatePreset;
use App\Http\Controllers\Controller;
use App\Models\BusinessAlert;
use App\Models\Budget;
use App\Models\ScheduledReport;
use App\Services\BusinessAdmin\BudgetService;
use App\Services\BusinessAdmin\BusinessAlertService;
use App\Services\BusinessAdmin\ExecutiveDashboardService;
use App\Services\BusinessAdmin\ReportExportService;
use App\Services\BusinessAdmin\ScheduledReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExecutiveController extends Controller
{
    public function __construct(
        private readonly ExecutiveDashboardService $executive,
        private readonly BusinessAlertService $alerts,
        private readonly BudgetService $budgets,
        private readonly ScheduledReportService $scheduledReports,
        private readonly ReportExportService $export,
    ) {}

    public function index(Request $request): View
    {
        $preset = ReportDatePreset::tryFrom($request->input('preset', 'this_month')) ?? ReportDatePreset::ThisMonth;
        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        return view('business-admin.executive.index', [
            'data' => $this->executive->build(
                $request->user(),
                $preset,
                $request->input('from'),
                $request->input('to'),
                $branchId,
            ),
            'preset' => $preset,
            'presets' => ReportDatePreset::cases(),
            'scheduledReports' => $this->scheduledReports->list($request->user()),
        ]);
    }

    public function print(Request $request): View
    {
        $preset = ReportDatePreset::tryFrom($request->input('preset', 'this_month')) ?? ReportDatePreset::ThisMonth;

        return view('business-admin.executive.print', [
            'data' => $this->executive->build($request->user(), $preset, $request->input('from'), $request->input('to')),
            'organization' => $request->user()->organization,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $preset = ReportDatePreset::tryFrom($request->input('preset', 'this_month')) ?? ReportDatePreset::ThisMonth;
        $data = $this->executive->build($request->user(), $preset);
        $rows = [];

        foreach ($data['kpis'] as $key => $kpi) {
            $rows[] = [ucwords(str_replace('_', ' ', $key)), '£'.number_format((float) $kpi['current'], 2), '£'.number_format((float) $kpi['previous'], 2), ($kpi['percent'] !== null ? $kpi['percent'].'%' : '—')];
        }

        $table = ['columns' => ['KPI', 'Current', 'Previous', 'Change %'], 'rows' => $rows];

        return $request->input('format') === 'excel'
            ? $this->export->excel($table, 'executive-summary.xls')
            : $this->export->csv($table, 'executive-summary.csv');
    }

    public function acknowledgeAlert(Request $request, BusinessAlert $alert): RedirectResponse
    {
        $this->authorize('update', $alert);
        $this->alerts->acknowledge($request->user(), $alert);

        return back()->with('status', 'Alert acknowledged.');
    }

    public function resolveAlert(Request $request, BusinessAlert $alert): RedirectResponse
    {
        $this->authorize('update', $alert);
        $this->alerts->resolve($request->user(), $alert);

        return back()->with('status', 'Alert resolved.');
    }

    public function storeBudget(Request $request): RedirectResponse
    {
        $this->authorize('create', Budget::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'branch_id' => ['nullable', 'integer'],
            'lines' => ['required', 'array'],
            'lines.*.category' => ['required', 'string'],
            'lines.*.amount' => ['required', 'numeric', 'min:0'],
        ]);

        $this->budgets->store($request->user(), $validated, $validated['lines']);

        return back()->with('status', 'Budget saved.');
    }

    public function storeScheduledReport(Request $request): RedirectResponse
    {
        $this->authorize('create', ScheduledReport::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'report_type' => ['required', 'string'],
            'frequency' => ['required', 'in:daily,weekly,monthly,quarterly,yearly'],
            'run_at' => ['nullable', 'date_format:H:i'],
            'recipients' => ['required', 'array'],
            'recipients.*' => ['email'],
            'branch_id' => ['nullable', 'integer'],
            'format' => ['nullable', 'in:email,csv,excel'],
        ]);

        $this->scheduledReports->store($request->user(), $validated);

        return back()->with('status', 'Scheduled report created.');
    }
}
