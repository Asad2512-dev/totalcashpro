<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kiosk;

use App\Enums\KioskActivityEvent;
use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\AttendanceService;
use App\Services\Kiosk\SmartKioskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class SmartKioskController extends Controller
{
    public function __construct(
        private readonly SmartKioskService $kiosk,
        private readonly AttendanceService $attendance,
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
        $session = $this->kiosk->requireActiveSession($request, $kiosk);
        $data = $request->validate(['pin' => ['required', 'digits:4']]);

        $admin = $session->startedBy;

        try {
            $state = $this->attendance->smartKioskPin($admin, $data['pin'], (int) $kiosk->branch_id);
        } catch (ValidationException $exception) {
            $this->kiosk->logActivity(
                kiosk: $kiosk,
                event: KioskActivityEvent::PinFailed,
                request: $request,
                meta: ['pin_length' => 4],
            );

            throw $exception;
        }

        $event = match ($state['action_performed']) {
            'clock-in' => KioskActivityEvent::ClockIn,
            'clock-out' => KioskActivityEvent::ClockOut,
            'start-break' => KioskActivityEvent::StartBreak,
            'end-break' => KioskActivityEvent::EndBreak,
            default => KioskActivityEvent::ClockIn,
        };

        $this->kiosk->logClockEvent(
            kiosk: $kiosk,
            event: $event,
            staff: $state['user'],
            request: $request,
            meta: [
                'action' => $state['action_performed'],
                'hours' => $state['hours'],
            ],
        );

        $user = $state['user'];

        return response()->json([
            'ok' => true,
            'action' => $state['action_performed'],
            'action_label' => $state['action_label'],
            'user' => [
                'name' => $user->name,
                'avatar_url' => $user->avatar_path ? Storage::url($user->avatar_path) : null,
            ],
            'hours' => $state['hours'],
            'time' => now()->format('g:i A'),
        ]);
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
}
