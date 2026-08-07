<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\BusinessAdmin\KioskService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When attendance kiosk mode is active, keep the device on kiosk routes only.
 */
final class EnsureKioskLock
{
    public function __construct(private readonly KioskService $kiosk) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->kiosk->isActive()) {
            return $next($request);
        }

        if ($request->routeIs(
            'business-admin.kiosk.index',
            'business-admin.kiosk.*',
            'logout',
        )) {
            return $next($request);
        }

        return redirect()->route('business-admin.kiosk.index');
    }
}
