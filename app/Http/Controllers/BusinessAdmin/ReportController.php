<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\ReportCenterService;
use App\Services\BusinessAdmin\ReportExportService;
use App\Services\BusinessAdmin\SavedReportService;
use App\Support\Reports\ReportCenterFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportController extends Controller
{
    public function __construct(
        private readonly ReportCenterService $reports,
        private readonly ReportExportService $export,
        private readonly SavedReportService $savedReports,
    ) {}

    public function index(Request $request): View
    {
        $filter = ReportCenterFilter::fromRequest($request);
        $report = $this->reports->build($request->user(), $filter);

        return view('business-admin.reports.index', [
            'report' => $report,
            'filter' => $filter,
            'savedReports' => $this->savedReports->list($request->user()),
        ]);
    }

    public function storeSaved(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $filter = ReportCenterFilter::fromRequest($request);
        $this->savedReports->save($request->user(), $filter, $validated['name']);

        return back()->with('status', 'Report saved.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filter = ReportCenterFilter::fromRequest($request);
        $report = $this->reports->build($request->user(), $filter);
        $format = $request->string('format', 'csv')->toString();
        $slug = $filter->reportType->value;
        $filename = "totalcashpro-{$slug}-{$filter->from}-to-{$filter->to}";

        return match ($format) {
            'excel', 'xls' => $this->export->excel($report['table'], "{$filename}.xls"),
            default => $this->export->csv($report['table'], "{$filename}.csv"),
        };
    }
}
