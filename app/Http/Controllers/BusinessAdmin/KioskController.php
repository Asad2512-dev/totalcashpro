<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\BusinessAdmin\AttendanceService;
use App\Services\BusinessAdmin\BranchContext;
use App\Services\BusinessAdmin\KioskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View as ViewResponse;

final class KioskController extends Controller
{
    public function __construct(
        private readonly KioskService $kiosk,
        private readonly AttendanceService $attendance,
        private readonly BranchContext $branches,
    ) {}

    public function index(Request $request): View|ViewResponse
    {
        $admin = $request->user();

        if ($this->kiosk->sessionExpired($admin)) {
            session()->forget([
                'attendance_kiosk.active',
                'attendance_kiosk.branch_id',
                'attendance_kiosk.opened_at',
                'attendance_kiosk.last_activity',
            ]);
        }

        if (! $this->kiosk->isActive()) {
            return view('business-admin.kiosk.launch', [
                'branches' => $this->branches->resolveBranches($admin),
                'settings' => $this->kiosk->settings($admin),
            ]);
        }

        $branch = $this->kiosk->requireActiveBranch($admin);
        $organization = $admin->organization;

        return view('business-admin.kiosk.index', [
            'branch' => $branch,
            'organization' => $organization,
            'settings' => $this->kiosk->settings($admin),
            'logoUrl' => $this->organizationLogoUrl($organization?->logo_path),
        ]);
    }

    public function settings(Request $request): View
    {
        return view('business-admin.kiosk.settings', [
            'settings' => $this->kiosk->settings($request->user()),
            'branches' => $this->branches->resolveBranches($request->user()),
            'kioskActive' => $this->kiosk->isActive(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'welcome_message' => ['required', 'string', 'max:255'],
            'session_timeout_minutes' => ['required', 'integer', 'min:30', 'max:1440'],
            'success_display_seconds' => ['required', 'integer', 'min:2', 'max:10'],
            'show_photos' => ['sometimes', 'boolean'],
        ]);

        $data['show_photos'] = $request->boolean('show_photos');

        $this->kiosk->updateSettings($request->user(), $data);

        return redirect()
            ->route('business-admin.kiosk.settings')
            ->with('status', 'Kiosk settings saved.');
    }

    public function activate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
        ]);

        $branch = Branch::query()
            ->where('organization_id', $request->user()->organization_id)
            ->whereKey($data['branch_id'])
            ->firstOrFail();

        $this->kiosk->activate($request->user(), $branch);

        return redirect()->route('business-admin.kiosk.index');
    }

    public function exit(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        try {
            $this->kiosk->deactivate($request->user(), $data['password']);
        } catch (ValidationException $exception) {
            if ($request->expectsJson()) {
                throw $exception;
            }

            return back()->withErrors($exception->errors());
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()
            ->route('business-admin.kiosk.settings')
            ->with('status', 'Kiosk mode ended.');
    }

    public function verify(Request $request): JsonResponse
    {
        $admin = $request->user();
        $branch = $this->kiosk->requireActiveBranch($admin);
        $data = $request->validate(['pin' => ['required', 'digits:4']]);

        try {
            $state = $this->attendance->verifyPinForBranch($admin, $data['pin'], (int) $branch->id);
        } catch (ValidationException $exception) {
            $this->kiosk->logPinFailure($admin, (int) $branch->id);

            throw $exception;
        }

        return response()->json($this->formatState($state));
    }

    public function action(Request $request): JsonResponse
    {
        $admin = $request->user();
        $branch = $this->kiosk->requireActiveBranch($admin);
        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
            'action' => ['required', 'in:clock-in,clock-out,start-break,end-break'],
        ]);

        try {
            $state = $this->attendance->actionForBranch($admin, $data['pin'], $data['action'], (int) $branch->id);
        } catch (ValidationException $exception) {
            if ($exception->errors()['pin'] ?? null) {
                $this->kiosk->logPinFailure($admin, (int) $branch->id);
            }

            throw $exception;
        }

        $this->kiosk->logClockEvent($admin, $state['user'], $data['action'], (int) $branch->id);

        return response()->json($this->formatState($state));
    }

    /**
     * @param  array{user: \App\Models\User, state: string, break: ?\App\Models\AttendanceBreak, hours: ?float}  $state
     * @return array<string, mixed>
     */
    private function formatState(array $state): array
    {
        $user = $state['user'];

        return [
            'state' => $state['state'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_path ? Storage::url($user->avatar_path) : null,
            ],
            'hours' => $state['hours'],
            'break_ends_at' => $state['break']?->break_ended_at?->toIso8601String(),
        ];
    }

    private function organizationLogoUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return brand_logo_url();
        }

        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return str_starts_with($path, '/') ? asset($path) : $path;
        }

        return Storage::url($path);
    }
}
