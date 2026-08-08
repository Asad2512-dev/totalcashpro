<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kiosk;

use App\Enums\KioskActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Services\Kiosk\KioskAttendanceService;
use App\Services\Kiosk\KioskV2Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class KioskV2Controller extends Controller
{
    public function __construct(
        private readonly KioskV2Service $kiosk,
        private readonly KioskAttendanceService $attendance,
    ) {}

    public function show(Request $request): View
    {
        $session = $this->kiosk->sessionFromRequest($request);
        $organization = null;
        $branches = collect();
        $branch = null;
        $settings = null;
        $attendance = [];

        if ($session !== null) {
            $organization = $session->organization;
            $settings = $this->kiosk->settingsFor($organization);
            $branches = Branch::query()
                ->where('organization_id', $session->organization_id)
                ->orderBy('name')
                ->get(['id', 'name']);

            if ($session->branch_id) {
                $branch = Branch::query()->find($session->branch_id);
                $context = $this->kiosk->contextFromSession($session);
                $attendance = $this->kiosk->currentAttendance($context);
            }
        }

        return view('kiosk.v2.index', [
            'session' => $session,
            'sessionActive' => $session !== null && $session->branch_id !== null,
            'needsBranch' => $session !== null && $session->branch_id === null,
            'sessionAdminEmail' => $session?->startedBy?->email,
            'organization' => $organization,
            'branches' => $branches,
            'branch' => $branch,
            'settings' => $settings,
            'attendance' => $attendance,
            'logoUrl' => $this->kiosk->organizationLogoUrl($organization?->logo_path),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::query()->where('email', $data['email'])->first();
        if ($admin === null || ! $admin->isAdmin() || ! $admin->organization_id) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $organization = $admin->organization;
        $result = $this->kiosk->loginAdmin($organization, $data['email'], $data['password'], $request);

        return response()
            ->json(['success' => true, 'data' => ['message' => 'Logged in. Select a branch to continue.']])
            ->cookie($result['cookie']);
    }

    public function selectBranch(Request $request): JsonResponse
    {
        $session = $this->kiosk->sessionFromRequest($request);
        if ($session === null) {
            abort(403, 'Kiosk session is not active.');
        }
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
        ]);

        $session = $this->kiosk->selectBranch($session, (int) $data['branch_id'], $request);
        $context = $this->kiosk->contextFromSession($session);

        return response()->json([
            'success' => true,
            'data' => [
                'branch' => ['id' => $context->branchId, 'name' => $session->branch?->name],
                'attendance' => $this->kiosk->currentAttendance($context),
            ],
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $session = $this->kiosk->sessionFromRequest($request);
        if ($session === null) {
            abort(403);
        }
        $organization = $session->organization;

        $data = $request->validate([
            'display_name' => ['nullable', 'string', 'max:120'],
            'show_attendance_list' => ['nullable', 'boolean'],
            'show_staff_names' => ['nullable', 'boolean'],
            'default_branch_id' => ['nullable', 'integer'],
        ]);

        if (isset($data['default_branch_id'])) {
            $valid = Branch::query()
                ->where('id', $data['default_branch_id'])
                ->where('organization_id', $organization->id)
                ->exists();
            if (! $valid) {
                throw ValidationException::withMessages(['default_branch_id' => 'Invalid branch.']);
            }
        }

        $settings = $this->kiosk->updateSettings($organization, $data);
        $this->kiosk->logActivity($session, KioskActivityEvent::SettingsChanged, $request, actor: $session->startedBy);

        return response()->json(['success' => true, 'data' => ['settings' => $settings]]);
    }

    public function pin(Request $request): JsonResponse
    {
        $session = $this->kiosk->requireActiveSession($request);
        $context = $this->kiosk->contextFromSession($session);
        $limitKey = 'kiosk-v2-pin:'.$session->organization_id.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($limitKey, 10)) {
            throw ValidationException::withMessages([
                'pin' => 'Too many PIN attempts. Please wait before trying again.',
            ]);
        }

        $data = $request->validate(['pin' => ['required', 'digits:4']]);

        try {
            $result = $this->attendance->authenticatePinForContext($context, $data['pin']);
        } catch (ValidationException $exception) {
            RateLimiter::hit($limitKey, 300);
            $this->kiosk->logActivity($session, KioskActivityEvent::PinFailed, $request, meta: ['pin_length' => 4]);
            throw $exception;
        }

        RateLimiter::clear($limitKey);

        if (($result['step'] ?? '') === 'success') {
            $staff = isset($result['user']['id']) ? User::query()->find($result['user']['id']) : null;
            if ($staff) {
                $event = match ($result['action'] ?? '') {
                    'clock-in' => KioskActivityEvent::ClockIn,
                    'clock-out' => KioskActivityEvent::ClockOut,
                    'start-break' => KioskActivityEvent::StartBreak,
                    'end-break' => KioskActivityEvent::EndBreak,
                    default => null,
                };
                if ($event) {
                    $this->kiosk->logActivity($session, $event, $request, staff: $staff);
                }
            }
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function action(Request $request): JsonResponse
    {
        $session = $this->kiosk->requireActiveSession($request);
        $context = $this->kiosk->contextFromSession($session);

        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
            'action' => ['required', 'string', Rule::in(['clock-out', 'start-break', 'end-break', 'choose-break'])],
            'break_type' => ['nullable', 'string'],
            'idempotency_key' => ['nullable', 'string', 'max:64'],
        ]);

        $result = $this->attendance->performActionForContext(
            $context,
            $data['pin'],
            $data['action'],
            $data['break_type'] ?? null,
            $data['idempotency_key'] ?? null,
        );

        if (($result['step'] ?? '') === 'success') {
            $staff = isset($result['user']['id']) ? User::query()->find($result['user']['id']) : null;
            if ($staff) {
                $event = match ($result['action'] ?? '') {
                    'clock-out' => KioskActivityEvent::ClockOut,
                    'start-break' => KioskActivityEvent::StartBreak,
                    'end-break' => KioskActivityEvent::EndBreak,
                    default => null,
                };
                if ($event) {
                    $this->kiosk->logActivity($session, $event, $request, staff: $staff);
                }
            }
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $session = $this->kiosk->requireActiveSession($request);
        $context = $this->kiosk->contextFromSession($session);

        return response()->json([
            'success' => true,
            'data' => ['attendance' => $this->kiosk->currentAttendance($context)],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $session = $this->kiosk->sessionFromRequest($request);
        if ($session === null) {
            return response()->json(['success' => true, 'data' => ['message' => 'No active session.']])
                ->cookie(\Illuminate\Support\Facades\Cookie::forget(KioskV2Service::COOKIE_NAME));
        }

        $admin = $session->startedBy;
        if ($admin === null) {
            abort(403);
        }

        $result = $this->kiosk->logout($session, $admin, $request);

        return response()
            ->json(['success' => true, 'data' => ['message' => 'Kiosk session ended.']])
            ->cookie($result['cookie']);
    }

    public function changeBranch(Request $request): JsonResponse
    {
        return $this->selectBranch($request);
    }
}
