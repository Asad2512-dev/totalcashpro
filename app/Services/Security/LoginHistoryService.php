<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

final class LoginHistoryService
{
    public function __construct(
        private readonly UserAgentParser $userAgentParser,
    ) {}

    public function recordSuccess(User $user, Request $request): LoginHistory
    {
        $parsed = $this->userAgentParser->parse($request->userAgent());

        return LoginHistory::query()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role?->slug,
            'ip_address' => $request->ip(),
            'browser' => $parsed['browser'],
            'device' => $parsed['device'],
            'operating_system' => $parsed['os'],
            'country' => null,
            'success' => true,
            'event_type' => 'login',
            'logged_in_at' => now(),
        ]);
    }

    public function recordLogout(User $user, Request $request): LoginHistory
    {
        $parsed = $this->userAgentParser->parse($request->userAgent());

        return LoginHistory::query()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role?->slug,
            'ip_address' => $request->ip(),
            'browser' => $parsed['browser'],
            'device' => $parsed['device'],
            'operating_system' => $parsed['os'],
            'success' => true,
            'event_type' => 'logout',
            'logged_in_at' => now(),
        ]);
    }

    public function recordFailure(?string $email, Request $request, string $reason): LoginHistory
    {
        $parsed = $this->userAgentParser->parse($request->userAgent());
        $user = $email ? User::query()->where('email', $email)->first() : null;

        return LoginHistory::query()->create([
            'user_id' => $user?->id,
            'email' => $email,
            'role' => $user?->role?->slug,
            'ip_address' => $request->ip(),
            'browser' => $parsed['browser'],
            'device' => $parsed['device'],
            'operating_system' => $parsed['os'],
            'success' => false,
            'failure_reason' => $reason,
            'logged_in_at' => now(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, LoginHistory>
     */
    public function forUser(User $user, int $perPage = 20)
    {
        return LoginHistory::query()
            ->where('user_id', $user->id)
            ->latest('logged_in_at')
            ->paginate($perPage);
    }
}
