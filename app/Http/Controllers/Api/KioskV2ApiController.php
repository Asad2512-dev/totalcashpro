<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kiosk\KioskV2Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Thin API wrapper around kiosk V2 web actions for future Flutter clients.
 */
final class KioskV2ApiController extends Controller
{
    public function __construct(private readonly KioskV2Controller $kiosk) {}

    public function authenticate(Request $request): JsonResponse
    {
        return $this->kiosk->login($request);
    }

    public function selectBranch(Request $request): JsonResponse
    {
        return $this->kiosk->selectBranch($request);
    }

    public function config(Request $request): JsonResponse
    {
        $session = app(\App\Services\Kiosk\KioskV2Service::class)->requireActiveSession($request);
        $context = app(\App\Services\Kiosk\KioskV2Service::class)->contextFromSession($session);
        $settings = app(\App\Services\Kiosk\KioskV2Service::class)->settingsFor($session->organization);

        return response()->json([
            'success' => true,
            'data' => [
                'branch' => $session->branch?->only(['id', 'name']),
                'settings' => $settings,
                'config' => $context->settings,
                'break_types' => app(\App\Services\Kiosk\KioskBreakTypeService::class)
                    ->kioskOptions($session->organization_id),
            ],
        ]);
    }

    public function pin(Request $request): JsonResponse
    {
        return $this->kiosk->pin($request);
    }

    public function action(Request $request): JsonResponse
    {
        return $this->kiosk->action($request);
    }

    public function attendance(Request $request): JsonResponse
    {
        return $this->kiosk->attendance($request);
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->kiosk->logout($request);
    }

    public function revoke(Request $request): JsonResponse
    {
        $session = app(\App\Services\Kiosk\KioskV2Service::class)->activeSessionForOrganization(
            (int) auth()->user()->organization_id,
        );

        if ($session === null) {
            return response()->json(['success' => false, 'message' => 'No active kiosk session.'], 404);
        }

        app(\App\Services\Kiosk\KioskV2Service::class)->revokeSession($session, auth()->user(), $request);

        return response()->json(['success' => true, 'data' => ['message' => 'Kiosk session revoked.']]);
    }
}
