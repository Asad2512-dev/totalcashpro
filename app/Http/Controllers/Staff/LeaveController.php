<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\LeaveStoreRequest;
use App\Services\Staff\StaffHrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LeaveController extends Controller
{
    public function __construct(private readonly StaffHrService $hr) {}

    public function index(Request $request): View
    {
        return view('staff.leave.index', [
            'requests' => $this->hr->leaveRequests($request->user()),
            'types' => \App\Enums\LeaveType::cases(),
        ]);
    }

    public function store(LeaveStoreRequest $request): RedirectResponse
    {
        $this->hr->submitLeave($request->user(), $request->validated());

        return back()->with('status', 'Leave request submitted.');
    }
}
