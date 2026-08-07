<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Organization;
use App\Models\User;
use App\Services\Logging\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class ImpersonationService implements ServiceInterface
{
    public const SESSION_IMPERSONATOR_ID = 'impersonator_id';

    public const SESSION_STARTED_AT = 'impersonation_started_at';

    public const SESSION_REASON = 'impersonation_reason';

    public const SESSION_ORGANIZATION_ID = 'impersonation_organization_id';

    public const SESSION_ORGANIZATION_NAME = 'impersonation_organization_name';

    private const TIMEOUT_MINUTES = 120;

    public function __construct(private readonly AuditLogger $audit) {}

    public function start(User $superAdmin, Organization $organization, string $reason, Request $request): User
    {
        if (! $superAdmin->isSuperAdmin()) {
            abort(403);
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => 'A reason is required to sign in as this business.']);
        }

        $owner = $organization->owner
            ?? User::query()
                ->where('organization_id', $organization->id)
                ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                ->orderBy('id')
                ->first();

        if ($owner === null) {
            throw ValidationException::withMessages(['organization' => 'This business has no admin owner to sign in as.']);
        }

        $request->session()->put(self::SESSION_IMPERSONATOR_ID, $superAdmin->id);
        $request->session()->put(self::SESSION_STARTED_AT, now()->toIso8601String());
        $request->session()->put(self::SESSION_REASON, $reason);
        $request->session()->put(self::SESSION_ORGANIZATION_ID, $organization->id);
        $request->session()->put(self::SESSION_ORGANIZATION_NAME, $organization->name);

        Auth::login($owner);
        $request->session()->regenerate();

        $this->audit->log(
            action: 'impersonation.started',
            user: $superAdmin,
            target: $organization,
            newValues: [
                'impersonated_user_id' => $owner->id,
                'reason' => $reason,
            ],
            request: $request,
        );

        return $owner;
    }

    public function stop(Request $request): void
    {
        $impersonatorId = $request->session()->get(self::SESSION_IMPERSONATOR_ID);
        if ($impersonatorId === null) {
            abort(403, 'Not in an impersonation session.');
        }

        $impersonator = User::query()->find($impersonatorId);
        $impersonated = Auth::user();

        $this->audit->log(
            action: 'impersonation.stopped',
            user: $impersonator,
            target: $impersonated,
            newValues: [
                'organization_id' => $request->session()->get(self::SESSION_ORGANIZATION_ID),
                'reason' => $request->session()->get(self::SESSION_REASON),
            ],
            request: $request,
        );

        $this->clearSession($request);

        if ($impersonator !== null) {
            Auth::login($impersonator);
            $request->session()->regenerate();
        } else {
            Auth::logout();
        }
    }

    public function isActive(Request $request): bool
    {
        return $request->session()->has(self::SESSION_IMPERSONATOR_ID);
    }

    /**
     * @return array{impersonator: User|null, organization_name: string|null, started_at: string|null, reason: string|null}
     */
    public function context(Request $request): array
    {
        if (! $this->isActive($request)) {
            return [
                'impersonator' => null,
                'organization_name' => null,
                'started_at' => null,
                'reason' => null,
            ];
        }

        $startedAt = $request->session()->get(self::SESSION_STARTED_AT);
        if ($startedAt !== null) {
            $startedAtCarbon = \Illuminate\Support\Carbon::parse($startedAt);
            if ($startedAtCarbon->diffInMinutes(now()) >= self::TIMEOUT_MINUTES) {
                $this->stop($request);

                return [
                    'impersonator' => null,
                    'organization_name' => null,
                    'started_at' => null,
                    'reason' => null,
                ];
            }
        }

        return [
            'impersonator' => User::query()->find($request->session()->get(self::SESSION_IMPERSONATOR_ID)),
            'organization_name' => $request->session()->get(self::SESSION_ORGANIZATION_NAME),
            'started_at' => $startedAt,
            'reason' => $request->session()->get(self::SESSION_REASON),
        ];
    }

    private function clearSession(Request $request): void
    {
        $request->session()->forget([
            self::SESSION_IMPERSONATOR_ID,
            self::SESSION_STARTED_AT,
            self::SESSION_REASON,
            self::SESSION_ORGANIZATION_ID,
            self::SESSION_ORGANIZATION_NAME,
        ]);
    }
}
