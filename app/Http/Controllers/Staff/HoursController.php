<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffHrService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HoursController extends Controller
{
    public function __construct(private readonly StaffHrService $hr) {}

    public function index(Request $request): View
    {
        $week = $this->hr->weeklyHours($request->user(), $request->input('week'));

        return view('staff.hours.index', [
            'weekStart' => $week['from']->toDateString(),
            'weekLabel' => $week['from']->format('d M').' – '.$week['to']->format('d M Y'),
            'days' => $week['days'],
            'totalHours' => $week['totalHours'],
        ]);
    }
}
