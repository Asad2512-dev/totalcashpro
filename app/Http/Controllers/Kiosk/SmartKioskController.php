<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kiosk;

use App\Enums\KioskActivityEvent;
use App\Enums\KioskSyncEventType;
use App\Http\Controllers\Controller;
use App\Services\Kiosk\KioskAttendanceService;
use App\Services\Kiosk\KioskSyncEventService;
use App\Services\Kiosk\SmartKioskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class SmartKioskController extends Controller
{
    public function __construct(
        private readonly SmartKioskService $kiosk,
        private readonly KioskAttendanceService $attendance,
        private readonly KioskSyncEventService $sync,
    ) {}

    public function show(Request $request, string $token): View
    {
        $kiosk = $this->kiosk->findByToken($token);
        $session = $this->kiosk->sessionFromRequest($request, $kiosk);
        if ($session !== null) {
            $session->load('startedBy');
        }
        $organization = $kiosk->organization;

        return view('kiosk.smart.index', [
            'kiosk' => $kiosk,
            'sessionActive' => $session !== null,
            'sessionAdminEmail' => $session?->startedBy?->email,
            'organization' => $organization,
            'branch' => $kiosk->branch,
            'logoUrl' => $this->kiosk->organizationLogoUrl($organization?->logo_path),
            'publicUrl' => $kiosk->publicUrl(),
        ]);
    }

    public function start(Request $request, string $token): JsonResponse
    {
        $kiosk = $this->kiosk->findByToken($token);
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = $this->kiosk->authenticateAdmin($data['email'], $data['password'], $kiosk);
        $result = $this->kiosk->startSession($kiosk, $admin, $request);

        return response()
            ->json(['ok' => true, 'message' => 'Kiosk started.'])
            ->cookie($result['cookie']);
    }

    public function pin(Request $request, string $token): JsonResponse
    {
        $kiosk = $this->kiosk->findByToken($token);
        $this->kiosk->requireActiveSession($request, $kiosk);
        $limitKey = 'kiosk-pin:'.$kiosk->id.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($limitKey, 10)) {
            throw ValidationException::withMessages([
                'pin' => 'Too many PIN attempts. Please wait before trying again.',
            ]);
        }

        $data = $request->validate(['pin' => ['required', 'digits:4']]);

        try {
            $result = $this->attendance->authenticatePin($kiosk, $data['pin']);
        } catch (ValidationException $exception) {
            RateLimiter::hit($limitKey, 300);
            $this->kiosk->logActivity(
                kiosk: $kiosk,
                event: KioskActivityEvent::PinFailed,
                request: $request,
                meta: ['pin_length' => 4],
            );

            throw $exception;
        }

        RateLimiter::clear($limitKey);

        if (($result['step'] ?? '') === 'success') {
            $staff = isset($result['user']['id'])
                ? \App\Models\User::query()->find($result['user']['id'])
                : null;
            $this->logAttendanceEvent($kiosk, $request, $result, $staff);
        } elseif (($result['step'] ?? '') === 'rota_restricted') {
            $this->kiosk->logActivity(
                kiosk: $kiosk,
                event: KioskActivityEvent::RotaRestricted,
                request: $request,
                meta: ['rota' => $result['rota'] ?? null],
            );
        }

        return response()->json($result);
    }

    public function action(Request $request, string $token): JsonResponse
    {
        $kiosk = $this->kiosk->findByToken($token);
        $this->kiosk->requireActiveSession($request, $kiosk);

        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
            'action' => ['required', 'string', Rule::in(['clock-out', 'start-break', 'end-break', 'clock-in-override'])],
            'break_type' => ['nullable', 'string'],
            'idempotency_key' => ['nullable', 'string', 'max:64'],
        ]);

        try {
            $result = $this->attendance->performAction(
                kiosk: $kiosk,
                pin: $data['pin'],
                action: $data['action'],
                breakType: $data['break_type'] ?? null,
                rotaOverride: $data['action'] === 'clock-in-override',
                idempotencyKey: $data['idempotency_key'] ?? null,
            );
        } catch (ValidationException $exception) {
            throw $exception;
        }

        $this->logAttendanceEvent($kiosk, $request, $result, $this->attendance->staffForPin($kiosk, $data['pin']));

        if ($data['action'] === 'clock-in-override') {
            $this->kiosk->logActivity(
                kiosk: $kiosk,
                event: KioskActivityEvent::RotaOverride,
                request: $request,
                meta: ['action' => $data['action']],
            );
        }

        return response()->json($result);
    }

    public function sync(Request $request, string $token): JsonResponse
    {
        $kiosk = $this->kiosk->findByToken($token);
        $this->kiosk->requireActiveSession($request, $kiosk);

        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
            'event_type' => ['required', Rule::enum(KioskSyncEventType::class)],
            'idempotency_key' => ['required', 'string', 'max:64'],
            'event_time' => ['nullable', 'date'],
            'break_type' => ['nullable', 'string'],
            'rota_override' => ['sometimes', 'boolean'],
            'client_sequence' => ['nullable', 'integer'],
        ]);

        $staffUser = $this->attendance->staffForPin($kiosk, $data['pin']);

        $result = $this->sync->process(
            kiosk: $kiosk,
            staff: $staffUser,
            type: KioskSyncEventType::from($data['event_type']),
            idempotencyKey: $data['idempotency_key'],
            payload: $data,
        );

        $this->logAttendanceEvent($kiosk, $request, $result, $staffUser);

        return response()->json($result);
    }

    public function exit(Request $request, string $token): JsonResponse
    {
        $kiosk = $this->kiosk->findByToken($token);
        $session = $this->kiosk->sessionFromRequest($request, $kiosk);
        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = $this->kiosk->authenticateAdminForExit(
            $data['password'],
            $kiosk,
            $session,
            $data['email'] ?? null,
        );
        $result = $this->kiosk->closeSession($kiosk, $admin, $request);

        return response()
            ->json(['ok' => true, 'message' => 'Kiosk closed.'])
            ->cookie($result['cookie']);
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function logAttendanceEvent(\App\Models\BranchKiosk $kiosk, Request $request, array $result, ?\App\Models\User $staff = null): void
    {
        $action = (string) ($result['action'] ?? '');
        $event = match ($action) {
            'clock-in', 'clock-in-override' => KioskActivityEvent::ClockIn,
            'clock-out' => KioskActivityEvent::ClockOut,
            'start-break' => KioskActivityEvent::StartBreak,
            'end-break' => KioskActivityEvent::EndBreak,
            default => null,
        };

        if ($event === null || $staff === null) {
            return;
        }

        $this->kiosk->logClockEvent(
            kiosk: $kiosk,
            event: $event,
            staff: $staff,
            request: $request,
            meta: ['action' => $action, 'hours' => $result['hours'] ?? null],
        );
    }
}
