<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\RotaShift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

final class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->input('week'))->startOfWeek()
            : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $shifts = RotaShift::query()
            ->with('rotaSection')
            ->where('user_id', $user->id)
            ->whereBetween('shift_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();

        return view('staff.shift.index', [
            'weekStart' => $weekStart->toDateString(),
            'weekLabel' => $weekStart->format('d M').' – '.$weekEnd->format('d M Y'),
            'shifts' => $shifts,
            'todayShift' => $shifts->first(fn ($s) => $s->shift_date->isSameDay(now())),
        ]);
    }
}
