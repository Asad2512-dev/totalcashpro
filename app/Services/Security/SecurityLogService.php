<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\SecurityLogEvent;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;

final class SecurityLogService
{
    public function log(
        SecurityLogEvent $event,
        ?User $user = null,
        ?string $description = null,
        ?Request $request = null,
        ?array $metadata = null,
    ): SecurityLog {
        return SecurityLog::query()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'description' => $description ?? $event->label(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, SecurityLog>
     */
    public function forUser(User $user, int $perPage = 20)
    {
        return SecurityLog::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }
}
