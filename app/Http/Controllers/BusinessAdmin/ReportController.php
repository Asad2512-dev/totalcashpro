<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\ReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function index(Request $request): View
    {
        $period = $request->string('period', 'weekly')->toString();
        $from = $request->input('from', now()->subDays(13)->toDateString());
        $to = $request->input('to', now()->toDateString());

        if ($period === 'weekly' && ! $request->filled('from')) {
            $from = now()->startOfWeek()->toDateString();
            $to = now()->endOfWeek()->toDateString();
        } elseif ($period === 'monthly' && ! $request->filled('from')) {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        } elseif ($period === 'daily' && ! $request->filled('from')) {
            $from = $request->input('date', now()->toDateString());
            $to = $from;
        } elseif ($period === 'custom') {
            $period = 'custom';
        }

        $data = $this->reports->aggregate($request->user(), $period, $from, $to);

        return view('business-admin.reports.index', array_merge($data, [
            'period' => $period,
            'from' => $from,
            'to' => $to,
        ]));
    }
}
