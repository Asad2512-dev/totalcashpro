<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Services\BusinessAdmin\RotaPrintService;
use App\Services\BusinessAdmin\RotaPublishingService;
use App\Services\BusinessAdmin\RotaService;
use App\Services\BusinessAdmin\RotaVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RotaController extends Controller
{
    public function __construct(
        private readonly RotaService $rota,
        private readonly RotaVersionService $versions,
        private readonly RotaPublishingService $publishing,
        private readonly RotaPrintService $print,
    ) {}

    public function index(Request $request): View
    {
        $weekStart = $request->input('week', now()->startOfWeek()->toDateString());
        $tab = $request->input('tab', 'weekly');
        if (! in_array($tab, ['weekly', 'sections', 'groups', 'history'], true)) {
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
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
        ]);

        $this->rota->storeShift($request->user(), $validated);

        return redirect()
            ->route('business-admin.rota', [
                'week' => $validated['shift_date'],
                'tab' => 'weekly',
            ])
            ->with('status', 'Shift saved to draft rota.');
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
            ->with('status', 'Shift removed from draft.');
    }

    public function finalize(Request $request, RotaVersion $version): RedirectResponse
    {
        $this->authorize('publish', $version);
        $request->validate(['confirm' => ['required', 'accepted']]);

        $this->publishing->finalize($request->user(), $version, $request);

        return back()->with('status', 'Rota finalized and ready to publish.');
    }

    public function publish(Request $request, RotaVersion $version): RedirectResponse
    {
        $this->authorize('publish', $version);
        $request->validate(['confirm' => ['required', 'accepted']]);

        $this->publishing->publish($request->user(), $version, $request);

        return back()->with('status', 'Rota published. Staff can now see this schedule.');
    }

    public function lock(Request $request, RotaVersion $version): RedirectResponse
    {
        $this->authorize('publish', $version);
        $this->publishing->lock($request->user(), $version, $request);

        return back()->with('status', 'Rota locked.');
    }

    public function archive(Request $request, RotaVersion $version): RedirectResponse
    {
        $this->authorize('publish', $version);
        $this->publishing->archive($request->user(), $version, $request);

        return back()->with('status', 'Rota archived.');
    }

    public function copyPreviousWeek(Request $request): RedirectResponse
    {
        $week = $request->validate(['week' => ['required', 'date']])['week'];
        $this->versions->copyPreviousWeek($request->user(), $week);

        return redirect()
            ->route('business-admin.rota', ['week' => $week, 'tab' => 'weekly'])
            ->with('status', 'Previous week copied into draft rota.');
    }

    public function clearWeek(Request $request, RotaVersion $version): RedirectResponse
    {
        $this->authorize('update', $version);
        $this->versions->clearWeek($request->user(), $version);

        return back()->with('status', 'Draft week cleared.');
    }

    public function print(Request $request, RotaVersion $version): View
    {
        $this->authorize('print', $version);

        if ((int) $version->organization_id !== (int) $request->user()->organization_id) {
            abort(403);
        }

        return view('business-admin.rota.print', $this->print->weekPrintData(
            $request->user(),
            $version,
            $request->input('section'),
            $request->integer('group') ?: null,
            $request->integer('staff') ?: null,
        ));
    }
}
