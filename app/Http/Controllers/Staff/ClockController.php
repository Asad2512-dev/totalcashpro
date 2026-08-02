<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\AttendanceService;
use App\Services\Logging\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ClockController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendance,
        private readonly ActivityLogger $activity,
    ) {}

    public function index(Request $request): View
    {
        $state = $this->attendance->currentStateForStaff($request->user());

        return view('staff.clock.index', [
            'state' => $state,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $state = $this->attendance->currentStateForStaff($request->user());

        return response()->json([
            'state' => $state['state'],
            'user' => ['id' => $state['user']->id, 'name' => $state['user']->name],
            'hours' => $state['hours'],
            'break_ends_at' => $state['break']?->break_ended_at?->toIso8601String(),
        ]);
    }

    public function action(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:clock-in,clock-out,start-break,end-break'],
        ]);

        $state = $this->attendance->actionForStaff($request->user(), $data['action']);

        $this->activity->log(
            event: 'attendance.'.$data['action'],
            description: $request->user()->name.' performed '.$data['action'],
            actor: $request->user(),
            subject: $request->user(),
        );

        return response()->json([
            'state' => $state['state'],
            'user' => ['id' => $state['user']->id, 'name' => $state['user']->name],
            'hours' => $state['hours'],
            'break_ends_at' => $state['break']?->break_ended_at?->toIso8601String(),
        ]);
    }
}
