<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\StaffStoreRequest;
use App\Http\Requests\BusinessAdmin\StaffUpdateRequest;
use App\Models\User;
use App\Services\BusinessAdmin\BranchContext;
use App\Services\BusinessAdmin\StaffService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StaffController extends Controller
{
    public function __construct(
        private readonly StaffService $staffService,
        private readonly BranchContext $branchContext,
    ) {}

    public function index(Request $request): View
    {
        return view('business-admin.staff.index', [
            'staff' => $this->staffService->list($request->user(), $request->input('q') ?? $request->input('search')),
        ]);
    }

    public function create(Request $request): View
    {
        return view('business-admin.staff.form', [
            'staffMember' => null,
            'branches' => $this->branchContext->resolveBranches($request->user()),
        ]);
    }

    public function store(StaffStoreRequest $request): RedirectResponse
    {
        $this->staffService->create($request->user(), $request->validated());

        return redirect()->route('business-admin.staff')->with('status', 'Staff member created. An invitation email has been sent.');
    }

    public function edit(Request $request, User $staff): View
    {
        abort_unless((int) $staff->organization_id === (int) $request->user()->organization_id, 403);

        return view('business-admin.staff.form', [
            'staffMember' => $staff,
            'branches' => $this->branchContext->resolveBranches($request->user()),
        ]);
    }

    public function update(StaffUpdateRequest $request, User $staff): RedirectResponse
    {
        $this->staffService->update($request->user(), $staff, $request->validated());

        return redirect()->route('business-admin.staff')->with('status', 'Staff member updated.');
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        $this->staffService->delete($request->user(), $staff);

        return redirect()->route('business-admin.staff')->with('status', 'Staff member removed.');
    }

    public function suspend(Request $request, User $staff): RedirectResponse
    {
        $this->staffService->suspend($request->user(), $staff);

        return back()->with('status', 'Staff member suspended.');
    }

    public function resetPassword(Request $request, User $staff): RedirectResponse
    {
        $this->staffService->resetPassword($request->user(), $staff);

        return back()->with('status', 'A new password has been emailed to the staff member.');
    }

    public function resetPin(Request $request, User $staff): RedirectResponse
    {
        $pin = $this->staffService->resetPin($request->user(), $staff);

        return back()
            ->with('status', 'A new kiosk PIN was generated for '.$staff->name.'. Copy it now — it will not be shown again.')
            ->with('generated_pin', $pin);
    }
}
