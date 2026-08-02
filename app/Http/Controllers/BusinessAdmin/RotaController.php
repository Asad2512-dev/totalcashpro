<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\RotaShift;
use App\Services\BusinessAdmin\RotaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RotaController extends Controller
{
    public function __construct(
        private readonly RotaService $rota,
    ) {}

    public function index(Request $request): View
    {
        $weekStart = $request->input('week', now()->startOfWeek()->toDateString());
        $tab = $request->input('tab', 'weekly');
        if (! in_array($tab, ['weekly', 'sections', 'groups'], true)) {
            $tab = 'weekly';
        }

        return view('business-admin.rota.index', array_merge(
            $this->rota->weekView($request->user(), $weekStart),
            ['tab' => $tab],
        ));
    }

    public function storeShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:rota_shifts,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'rota_section_id' => ['required', 'integer', 'exists:rota_sections,id'],
            'shift_type' => ['required', 'string', 'in:Morning,Evening'],
        ]);

        $this->rota->storeShift($request->user(), $validated);

        return redirect()
            ->route('business-admin.rota', [
                'week' => $validated['shift_date'],
                'tab' => 'weekly',
            ])
            ->with('status', 'Shift saved.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $this->rota->storeSection($request->user(), $validated);

        return redirect()
            ->route('business-admin.rota', [
                'week' => $request->input('week', now()->startOfWeek()->toDateString()),
                'tab' => 'sections',
            ])
            ->with('status', 'Section created.');
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $this->rota->storeGroup($request->user(), $validated);

        return redirect()
            ->route('business-admin.rota', [
                'week' => $request->input('week', now()->startOfWeek()->toDateString()),
                'tab' => 'groups',
            ])
            ->with('status', 'Group created.');
    }

    public function destroyShift(Request $request, RotaShift $shift): RedirectResponse
    {
        $this->rota->destroyShift($request->user(), (int) $shift->id);

        return redirect()
            ->route('business-admin.rota', [
                'week' => $request->input('week', $shift->shift_date?->toDateString()),
                'tab' => 'weekly',
            ])
            ->with('status', 'Shift removed.');
    }
}
