<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\KioskBreakType;
use App\Services\Kiosk\KioskBreakTypeService;
use App\Services\Kiosk\KioskV2Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class KioskSettingsController extends Controller
{
    public function __construct(
        private readonly KioskV2Service $kiosk,
        private readonly KioskBreakTypeService $breakTypes,
    ) {}

    public function index(Request $request): View
    {
        $organization = $request->user()->organization;
        $settings = $this->kiosk->settingsFor($organization);
        $branches = Branch::query()
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();
        $activeSession = $this->kiosk->activeSessionForOrganization($organization->id);

        return view('business-admin.kiosk.settings', [
            'settings' => $settings,
            'branches' => $branches,
            'activeSession' => $activeSession,
            'breakTypes' => KioskBreakType::query()
                ->where('organization_id', $organization->id)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get(),
            'kioskUrl' => url('/kiosk'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $organization = $request->user()->organization;

        $data = $request->validate([
            'display_name' => ['nullable', 'string', 'max:120'],
            'show_attendance_list' => ['nullable', 'boolean'],
            'show_staff_names' => ['nullable', 'boolean'],
            'default_branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->where('organization_id', $organization->id)],
            'session_lifetime_minutes' => ['nullable', 'integer', 'min:30', 'max:1440'],
            'success_delay_seconds' => ['nullable', 'integer', 'min:1', 'max:15'],
        ]);

        $this->kiosk->updateSettings($organization, $data);

        return back()->with('status', 'Kiosk settings saved.');
    }

    public function revokeSession(Request $request): RedirectResponse
    {
        $organization = $request->user()->organization;
        $session = $this->kiosk->activeSessionForOrganization($organization->id);

        if ($session === null) {
            return back()->with('error', 'No active kiosk session.');
        }

        $this->kiosk->revokeSession($session, $request->user(), $request);

        return back()->with('status', 'Kiosk session revoked.');
    }
}
