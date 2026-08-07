<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\LeaveStoreRequest;
use App\Http\Requests\Staff\ShiftSwapStoreRequest;
use App\Services\Staff\StaffHrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AvailabilityController extends Controller
{
    public function __construct(private readonly StaffHrService $hr) {}

    public function index(Request $request): View
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $existing = $this->hr->availability($request->user())->keyBy('day_of_week');

        return view('staff.availability.index', [
            'days' => $days,
            'existing' => $existing,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'availability' => ['required', 'array'],
            'availability.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'availability.*.is_available' => ['nullable', 'boolean'],
            'availability.*.start_time' => ['nullable', 'date_format:H:i'],
            'availability.*.end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $this->hr->saveAvailability($request->user(), $validated['availability']);

        return back()->with('status', 'Availability updated.');
    }
}
