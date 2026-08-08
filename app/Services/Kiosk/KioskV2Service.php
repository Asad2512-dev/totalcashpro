<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\AttendanceLogType;
use App\Enums\KioskActivityEvent;
use App\Enums\KioskSessionStatus;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\KioskActivityLog;
use App\Models\KioskSession;
use App\Models\Organization;
use App\Models\OrganizationKioskSetting;
use App\Models\User;
use App\Support\Kiosk\KioskContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

final class KioskV2Service implements ServiceInterface
{
    public const COOKIE_NAME = 'tcp_kiosk_session';

    private const COOKIE_MINUTES = 5256000;

    public function __construct(
        private readonly KioskConfigurationService $config,
        private readonly KioskAttendanceService $attendance,
        private readonly KioskBreakTypeService $breakTypes,
    ) {}

    public function settingsFor(Organization $organization): OrganizationKioskSetting
    {
        $settings = OrganizationKioskSetting::query()->firstOrCreate(
            ['organization_id' => $organization->id],
            [
                'display_name' => 'Staff Clock',
                'show_attendance_list' => true,
                'show_staff_names' => true,
                'success_delay_seconds' => 3,
                'session_lifetime_minutes' => 480,
                'is_active' => true,
            ],
        );

        $this->breakTypes->ensureDefaults($organization->id);

        return $settings;
    }

    public function sessionFromRequest(Request $request): ?KioskSession
    {
        $token = $request->cookie(self::COOKIE_NAME);
        if ($token === null || $token === '') {
            return null;
        }

        $session = KioskSession::query()
            ->with(['startedBy', 'organization'])
            ->where('session_token', $token)
            ->where('status', KioskSessionStatus::Active->value)
            ->whereNull('ended_at')
            ->first();

        if ($session === null) {
            return null;
        }

        if ($this->sessionExpired($session)) {
            $this->expireSession($session);

            return null;
        }

        $session->update(['last_activity_at' => now()]);

        return $session->fresh(['startedBy', 'organization']);
    }

    public function requireActiveSession(Request $request): KioskSession
    {
        $session = $this->sessionFromRequest($request);

        if ($session === null) {
            abort(403, 'Kiosk session is not active. A business admin must log in.');
        }

        if ($session->branch_id === null) {
            abort(403, 'Select a branch to continue.');
        }

        return $session;
    }

    /**
     * @return array{session: KioskSession, cookie: SymfonyCookie}
     */
    public function loginAdmin(Organization $organization, string $email, string $password, Request $request): array
    {
        $kioskSettings = $this->settingsFor($organization);

        if (! $kioskSettings->is_active) {
            throw ValidationException::withMessages(['email' => 'Kiosk is disabled for this organisation.']);
        }

        $admin = User::query()->where('email', $email)->first();

        if ($admin === null || ! Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        $this->assertBusinessAdmin($admin, $organization);

        $this->endActiveSessionsForOrganization($organization->id, $admin, KioskSessionStatus::LoggedOut);

        $session = KioskSession::query()->create([
            'organization_id' => $organization->id,
            'branch_id' => $kioskSettings->default_branch_id,
            'session_token' => Str::random(64),
            'started_by_user_id' => $admin->id,
            'status' => KioskSessionStatus::Active->value,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'device_summary' => $this->deviceSummary($request->userAgent()),
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);

        $this->logActivity($session, KioskActivityEvent::KioskStarted, $request, actor: $admin);

        return [
            'session' => $session,
            'cookie' => $this->makeSessionCookie($request, $session->session_token),
        ];
    }

    public function selectBranch(KioskSession $session, int $branchId, Request $request): KioskSession
    {
        $branch = Branch::query()
            ->where('id', $branchId)
            ->where('organization_id', $session->organization_id)
            ->first();

        if ($branch === null) {
            throw ValidationException::withMessages(['branch_id' => 'Invalid branch selection.']);
        }

        $session->update([
            'branch_id' => $branch->id,
            'last_activity_at' => now(),
        ]);

        $this->logActivity($session->fresh(), KioskActivityEvent::BranchChanged, $request, meta: [
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
        ]);

        return $session->fresh();
    }

    public function contextFromSession(KioskSession $session): KioskContext
    {
        if ($session->organization_id === null || $session->branch_id === null) {
            throw ValidationException::withMessages(['session' => 'Kiosk session is not fully configured.']);
        }

        $organization = Organization::query()->findOrFail($session->organization_id);
        $kioskSettings = $this->settingsFor($organization);
        $settings = $this->config->forContext($session->organization_id, $session->branch_id, $kioskSettings);

        return new KioskContext(
            organizationId: $session->organization_id,
            branchId: $session->branch_id,
            settings: $settings,
            session: $session,
            kioskSettings: $kioskSettings,
        );
    }

    /**
     * @return array{cleared: bool, cookie: SymfonyCookie}
     */
    public function logout(KioskSession $session, User $admin, Request $request): array
    {
        $this->assertBusinessAdmin($admin, Organization::query()->findOrFail($session->organization_id));

        $session->update([
            'status' => KioskSessionStatus::LoggedOut->value,
            'ended_at' => now(),
            'ended_by_user_id' => $admin->id,
        ]);

        $this->logActivity($session, KioskActivityEvent::KioskClosed, $request, actor: $admin);

        return [
            'cleared' => true,
            'cookie' => Cookie::forget(self::COOKIE_NAME),
        ];
    }

    public function revokeSession(KioskSession $session, User $admin, Request $request): void
    {
        $this->assertBusinessAdmin($admin, Organization::query()->findOrFail($session->organization_id));

        $session->update([
            'status' => KioskSessionStatus::Revoked->value,
            'ended_at' => now(),
            'revoked_at' => now(),
            'revoked_by_user_id' => $admin->id,
            'ended_by_user_id' => $admin->id,
        ]);

        $this->logActivity($session, KioskActivityEvent::ForceLogout, $request, actor: $admin);
    }

    public function activeSessionForOrganization(int $organizationId): ?KioskSession
    {
        return KioskSession::query()
            ->with(['startedBy', 'branch'])
            ->where('organization_id', $organizationId)
            ->where('status', KioskSessionStatus::Active->value)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function currentAttendance(KioskContext $context): array
    {
        if (! $context->showAttendanceList()) {
            return [];
        }

        $today = now()->toDateString();
        $staffIds = User::query()
            ->where('organization_id', $context->organizationId)
            ->where('branch_id', $context->branchId)
            ->where('status', 'active')
            ->pluck('id');

        $rows = [];

        foreach ($staffIds as $staffId) {
            $staff = User::query()->find($staffId);
            if ($staff === null) {
                continue;
            }

            $state = $this->attendance->resolveState($staff);
            if (! in_array($state['state'], ['checked_in', 'on_break'], true)) {
                continue;
            }

            $clockIn = AttendanceLog::query()
                ->where('user_id', $staff->id)
                ->where('branch_id', $context->branchId)
                ->whereDate('logged_at', $today)
                ->where('type', AttendanceLogType::ClockIn->value)
                ->latest('logged_at')
                ->first();

            $status = $state['state'] === 'on_break' ? 'On Break' : 'Working';
            $breakLabel = null;
            if ($state['break'] !== null) {
                $breakLabel = $state['break']->break_type instanceof \App\Enums\BreakType
                    ? $state['break']->break_type->label()
                    : (string) $state['break']->break_type;
            }

            $rows[] = [
                'name' => $staff->name,
                'status' => $status,
                'break_label' => $breakLabel,
                'clocked_in_at' => $clockIn?->logged_at?->format('H:i'),
            ];
        }

        return $rows;
    }

    public function updateSettings(Organization $organization, array $data): OrganizationKioskSetting
    {
        $settings = $this->settingsFor($organization);
        $settings->update([
            'default_branch_id' => $data['default_branch_id'] ?? $settings->default_branch_id,
            'display_name' => $data['display_name'] ?? $settings->display_name,
            'show_attendance_list' => (bool) ($data['show_attendance_list'] ?? $settings->show_attendance_list),
            'show_staff_names' => (bool) ($data['show_staff_names'] ?? $settings->show_staff_names),
            'success_delay_seconds' => (int) ($data['success_delay_seconds'] ?? $settings->success_delay_seconds),
            'session_lifetime_minutes' => (int) ($data['session_lifetime_minutes'] ?? $settings->session_lifetime_minutes),
        ]);

        return $settings->fresh();
    }

    public function organizationLogoUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return brand_logo_url();
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset($path);
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }

    public function logActivity(
        KioskSession $session,
        KioskActivityEvent $event,
        Request $request,
        ?User $staff = null,
        ?User $actor = null,
        array $meta = [],
    ): KioskActivityLog {
        return KioskActivityLog::query()->create([
            'branch_kiosk_id' => null,
            'organization_id' => $session->organization_id,
            'branch_id' => $session->branch_id,
            'event' => $event->value,
            'staff_user_id' => $staff?->id,
            'actor_user_id' => $actor?->id,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'device_summary' => $this->deviceSummary($request->userAgent()),
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }

    public function resolveOrganization(Request $request): Organization
    {
        $host = $request->getHost();
        $org = Organization::query()->where('slug', $request->query('org'))->first();

        if ($org !== null) {
            return $org;
        }

        if (auth()->check() && auth()->user()?->organization_id) {
            return Organization::query()->findOrFail(auth()->user()->organization_id);
        }

        $session = $this->sessionFromRequest($request);
        if ($session?->organization_id) {
            return Organization::query()->findOrFail($session->organization_id);
        }

        $defaultSlug = config('app.kiosk_default_org');
        if ($defaultSlug) {
            $org = Organization::query()->where('slug', $defaultSlug)->first();
            if ($org) {
                return $org;
            }
        }

        $first = Organization::query()->where('status', 'active')->orderBy('id')->first();
        if ($first !== null) {
            return $first;
        }

        abort(404, 'No organisation configured for kiosk.');
    }

    private function assertBusinessAdmin(User $user, Organization $organization): void
    {
        if (! $user->isAdmin() || (int) $user->organization_id !== (int) $organization->id) {
            abort(403, 'Only a business admin for this organisation can manage the kiosk.');
        }
    }

    private function endActiveSessionsForOrganization(int $organizationId, User $admin, KioskSessionStatus $status): void
    {
        KioskSession::query()
            ->where('organization_id', $organizationId)
            ->where('status', KioskSessionStatus::Active->value)
            ->whereNull('ended_at')
            ->update([
                'status' => $status->value,
                'ended_at' => now(),
                'ended_by_user_id' => $admin->id,
            ]);
    }

    private function sessionExpired(KioskSession $session): bool
    {
        if ($session->organization_id === null) {
            return false;
        }

        $settings = OrganizationKioskSetting::query()
            ->where('organization_id', $session->organization_id)
            ->first();

        $lifetime = $settings?->session_lifetime_minutes ?? 480;
        $last = $session->last_activity_at ?? $session->started_at;

        return $last !== null && $last->addMinutes($lifetime)->isPast();
    }

    private function expireSession(KioskSession $session): void
    {
        $session->update([
            'status' => KioskSessionStatus::Expired->value,
            'ended_at' => now(),
        ]);
    }

    private function makeSessionCookie(Request $request, string $token): SymfonyCookie
    {
        return Cookie::make(
            name: self::COOKIE_NAME,
            value: $token,
            minutes: self::COOKIE_MINUTES,
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'Strict',
        );
    }

    private function deviceSummary(?string $userAgent): string
    {
        if ($userAgent === null || $userAgent === '') {
            return 'Unknown device';
        }

        if (str_contains($userAgent, 'iPad')) {
            return 'iPad';
        }

        if (str_contains($userAgent, 'Android') && ! str_contains($userAgent, 'Mobile')) {
            return 'Android Tablet';
        }

        if (str_contains($userAgent, 'Android')) {
            return 'Android';
        }

        if (str_contains($userAgent, 'iPhone')) {
            return 'iPhone';
        }

        if (str_contains($userAgent, 'Macintosh')) {
            return 'Mac';
        }

        if (str_contains($userAgent, 'Windows')) {
            return 'Windows PC';
        }

        return 'Web Browser';
    }
}
