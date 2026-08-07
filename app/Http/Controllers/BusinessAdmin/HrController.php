<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\BusinessAdmin\HrAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HrController extends Controller
{
    public function __construct(private readonly HrAdminService $hr) {}

    public function index(Request $request): View
    {
        return view('business-admin.hr.index', [
            'leaveRequests' => $this->hr->pendingLeave($request->user()),
            'shiftSwaps' => $this->hr->pendingShiftSwaps($request->user()),
        ]);
    }

    public function reviewShiftSwap(Request $request, \App\Models\ShiftSwapRequest $shiftSwapRequest): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        $this->hr->reviewShiftSwap($request->user(), $shiftSwapRequest, $validated['action']);

        return back()->with('status', 'Shift swap request updated.');
    }

    public function reviewLeave(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('review', $leaveRequest);
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->hr->reviewLeave($request->user(), $leaveRequest, $validated['action'], $validated['admin_notes'] ?? null);

        return back()->with('status', 'Leave request updated.');
    }
}
