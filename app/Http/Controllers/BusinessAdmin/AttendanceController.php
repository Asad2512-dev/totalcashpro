<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\AttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance) {}

    public function index(Request $request): View
    {
        $week = $request->input('week', $request->input('start'));
        $report = $this->attendance->weeklyReport($request->user(), $week);

        return view('business-admin.attendance.index', array_merge($report, [
            'weekStart' => $report['from']->toDateString(),
        ]));
    }

    public function updateEntries(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'slots' => ['nullable', 'array'],
            'slots.*.in' => ['nullable', 'date_format:H:i'],
            'slots.*.out' => ['nullable', 'date_format:H:i'],
        ]);

        $this->attendance->replaceDayEntries(
            $request->user(),
            (int) $data['user_id'],
            $data['date'],
            $data['slots'] ?? [],
        );

        return back()->with('status', 'Attendance updated.');
    }
}
