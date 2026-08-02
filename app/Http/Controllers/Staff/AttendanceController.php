<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance) {}

    public function index(Request $request): View
    {
        $week = $this->attendance->personalWeek($request->user(), $request->input('week'));

        return view('staff.attendance.index', $week);
    }
}
