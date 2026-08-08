<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\RotaPrintService;
use App\Services\Staff\StaffRotaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ShiftController extends Controller
{
    public function __construct(
        private readonly StaffRotaService $rota,
        private readonly RotaPrintService $print,
    ) {}

    public function index(Request $request): View
    {
        $weekStart = $request->input('week', now()->startOfWeek()->toDateString());

        return view('staff.shift.index', $this->rota->weekView($request->user(), $weekStart));
    }

    public function print(Request $request): View
    {
        $weekStart = $request->input('week', now()->startOfWeek()->toDateString());

        return view('staff.shift.print', $this->print->staffPrintData($request->user(), $weekStart));
    }
}
