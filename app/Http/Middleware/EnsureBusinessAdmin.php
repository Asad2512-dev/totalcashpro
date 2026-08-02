<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureBusinessAdmin
{
    /**
     * Ensure the user is an active admin with an organization.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->isActive()) {
            abort(403, 'Your account is not active.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'You must be an admin to access this area.');
        }

        if ($user->organization_id === null) {
            abort(403, 'You must belong to an organization to access this area.');
        }

        return $next($request);
    }
}
