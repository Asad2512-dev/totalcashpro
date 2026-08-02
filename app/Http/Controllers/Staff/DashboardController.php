<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly StaffDashboardService $dashboard) {}

    public function __invoke(Request $request): View
    {
        return view('staff.dashboard', $this->dashboard->overview($request->user()));
    }
}
