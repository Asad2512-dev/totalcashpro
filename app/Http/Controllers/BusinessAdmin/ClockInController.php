<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\AttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ClockInController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance) {}

    public function index(): View
    {
        return view('business-admin.clock-in.index');
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate(['pin' => ['required', 'digits:4']]);
        $state = $this->attendance->verifyPin($request->user(), $data['pin']);

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
            'pin' => ['required', 'digits:4'],
            'action' => ['required', 'in:clock-in,clock-out,start-break,end-break'],
        ]);
        $state = $this->attendance->action($request->user(), $data['pin'], $data['action']);

        return response()->json([
            'state' => $state['state'],
            'user' => ['id' => $state['user']->id, 'name' => $state['user']->name],
            'hours' => $state['hours'],
            'break_ends_at' => $state['break']?->break_ended_at?->toIso8601String(),
        ]);
    }
}
