<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ShiftSwapStoreRequest;
use App\Services\Staff\StaffHrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ShiftSwapController extends Controller
{
    public function __construct(private readonly StaffHrService $hr) {}

    public function index(Request $request): View
    {
        return view('staff.shift-swap.index', [
            'requests' => $this->hr->shiftSwapRequests($request->user()),
            'shifts' => $this->hr->upcomingSwappableShifts($request->user()),
        ]);
    }

    public function store(ShiftSwapStoreRequest $request): RedirectResponse
    {
        $this->hr->submitShiftSwap($request->user(), $request->validated());

        return back()->with('status', 'Shift swap request submitted.');
    }
}
