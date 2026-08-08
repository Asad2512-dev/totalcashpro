<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Enums\CashDrawerStatus;
use App\Http\Controllers\Controller;
use App\Models\CashDrawer;
use App\Models\CashDrawerSession;
use App\Services\BusinessAdmin\BranchContext;
use App\Services\BusinessAdmin\CashDrawerService;
use App\Services\BusinessAdmin\CashMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CashDrawerController extends Controller
{
    public function __construct(
        private readonly CashDrawerService $drawers,
        private readonly CashMovementService $movements,
        private readonly BranchContext $branchContext,
    ) {}

    public function index(Request $request): View
    {
        $branchId = $request->integer('branch_id') ?: $this->branchContext->currentBranchId($request->user());
        $data = $this->drawers->dashboard($request->user(), $branchId);

        return view('business-admin.cash-drawers.index', [
            'drawers' => $data['drawers'],
            'summary' => $data['summary'],
            'branches' => $request->user()->organization?->branches()->orderBy('name')->get() ?? collect(),
            'selectedBranchId' => $branchId,
            'defaultFloat' => $this->drawers->organizationDefaultFloat($request->user()->organization),
            'staff' => $request->user()->organization?->users()->orderBy('name')->get() ?? collect(),
        ]);
    }

    public function show(Request $request, CashDrawer $drawer): View
    {
        $this->authorize('view', $drawer);

        $period = $request->string('period', 'daily')->toString();
        $detail = $this->drawers->detail(
            $request->user(),
            $drawer,
            $period,
            $request->string('date')->toString() ?: null,
        );

        $branchDrawers = $this->drawers->list($request->user(), false, $drawer->branch_id)
            ->where('id', '!=', $drawer->id)
            ->values();

        return view('business-admin.cash-drawers.show', array_merge($detail, [
            'transferTargets' => $branchDrawers,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CashDrawer::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:30'],
            'branch_id' => ['nullable', 'integer'],
            'default_opening_float' => ['nullable', 'numeric', 'min:0'],
            'variance_threshold' => ['nullable', 'numeric', 'min:0'],
            'assigned_user_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->drawers->create($request->user(), $validated);

        return back()->with('status', 'Till created.');
    }

    public function update(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:30'],
            'default_opening_float' => ['nullable', 'numeric', 'min:0'],
            'variance_threshold' => ['nullable', 'numeric', 'min:0'],
            'assigned_user_id' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->drawers->update($request->user(), $drawer, $validated);

        return back()->with('status', 'Till updated.');
    }

    public function activate(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);
        $this->drawers->setStatus($request->user(), $drawer, CashDrawerStatus::Active);

        return back()->with('status', 'Till activated.');
    }

    public function deactivate(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);
        $this->drawers->setStatus($request->user(), $drawer, CashDrawerStatus::Inactive);

        return back()->with('status', 'Till deactivated.');
    }

    public function lock(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);
        $this->drawers->setStatus($request->user(), $drawer, CashDrawerStatus::Locked);

        return back()->with('status', 'Till locked.');
    }

    public function unlock(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);
        $this->drawers->setStatus($request->user(), $drawer, CashDrawerStatus::Active);

        return back()->with('status', 'Till unlocked.');
    }

    public function open(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('update', $drawer);

        $validated = $request->validate([
            'opening_float' => ['nullable', 'numeric', 'min:0'],
            'opening_count' => ['nullable', 'array'],
            'opening_count.*.label' => ['nullable', 'string'],
            'opening_count.*.qty' => ['nullable', 'integer', 'min:0'],
            'opening_count.*.value' => ['nullable', 'numeric', 'min:0'],
            'float_adjustment_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->drawers->openDrawer(
            $request->user(),
            $drawer,
            isset($validated['opening_float']) ? (float) $validated['opening_float'] : null,
            $validated['opening_count'] ?? null,
        );

        return back()->with('status', 'Till opened.');
    }

    public function close(Request $request, CashDrawerSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'expected_cash' => ['required', 'numeric', 'min:0'],
            'variance_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->drawers->closeDrawer(
            $request->user(),
            $session,
            (float) $validated['actual_cash'],
            (float) $validated['expected_cash'],
            $validated['variance_reason'] ?? null,
        );

        return back()->with('status', 'Till closed.');
    }

    public function transfer(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $this->authorize('transfer', $drawer);

        $validated = $request->validate([
            'to_drawer_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $to = $this->drawers->findForBranch($request->user(), (int) $validated['to_drawer_id']);

        if ((int) $to->id === (int) $drawer->id) {
            return back()->with('error', 'Source and destination till must be different.');
        }

        $this->movements->transfer(
            $request->user(),
            $drawer,
            $to,
            (float) $validated['amount'],
            $validated['reason'],
        );

        return back()->with('status', 'Transfer recorded.');
    }
}
