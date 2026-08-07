<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ImpersonationController extends Controller
{
    public function __construct(private readonly ImpersonationService $impersonation) {}

    public function stop(Request $request): RedirectResponse
    {
        $this->impersonation->stop($request);

        return redirect()
            ->route('super-admin.dashboard')
            ->with('status', 'Impersonation ended. You are signed in as Super Admin again.');
    }
}
