<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\BranchKiosk;
use App\Services\Kiosk\BranchKioskManagementService;
use App\Services\Kiosk\SmartKioskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class BranchKioskController extends Controller
{
    public function __construct(
        private readonly BranchKioskManagementService $kiosks,
        private readonly SmartKioskService $smartKiosk,
    ) {}

    public function index(Request $request): View
    {
        return view('business-admin.kiosks.index', [
            'kiosks' => $this->kiosks->listForOrganization($request->user()),
            'branches' => $this->kiosks->branchesForOrganization($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $branch = \App\Models\Branch::query()
            ->where('organization_id', $request->user()->organization_id)
            ->whereKey($data['branch_id'])
            ->firstOrFail();

        $kiosk = $this->kiosks->create($request->user(), $branch, $data['name'] ?? null);

        return redirect()
            ->route('business-admin.kiosks.index')
            ->with('status', 'Kiosk created for '.$branch->name.'. URL: '.$kiosk->publicUrl());
    }

    public function update(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $this->kiosks->update($request->user(), $kiosk, $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'welcome_message' => ['required', 'string', 'max:255'],
            'show_photos' => ['sometimes', 'boolean'],
        ]) + ['show_photos' => $request->boolean('show_photos')]);

        return back()->with('status', 'Kiosk updated.');
    }

    public function regenerateToken(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $kiosk = $this->kiosks->regenerateToken($request->user(), $kiosk, $request);

        return back()->with('status', 'Token regenerated. New URL: '.$kiosk->publicUrl());
    }

    public function disable(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $this->kiosks->setEnabled($request->user(), $kiosk, false);
        $this->smartKiosk->forceLogout($kiosk, $request->user(), $request);

        return back()->with('status', 'Kiosk disabled.');
    }

    public function enable(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $this->kiosks->setEnabled($request->user(), $kiosk, true);

        return back()->with('status', 'Kiosk enabled.');
    }

    public function reset(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $this->kiosks->reset($request->user(), $kiosk, $request);

        return back()->with('status', 'Kiosk reset to defaults.');
    }

    public function forceLogout(Request $request, BranchKiosk $kiosk): RedirectResponse
    {
        $this->smartKiosk->forceLogout($kiosk, $request->user(), $request);

        return back()->with('status', 'Active kiosk session ended.');
    }

    public function activity(Request $request, BranchKiosk $kiosk): View
    {
        return view('business-admin.kiosks.activity', [
            'kiosk' => $kiosk,
            'logs' => $this->kiosks->activity($request->user(), $kiosk),
        ]);
    }
}
