<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\KioskActivityEvent;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\KioskActivityLog;
use App\Models\KioskSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

final class SmartKioskService implements ServiceInterface
{
    public const COOKIE_NAME = 'tcp_kiosk_session';

    private const COOKIE_MINUTES = 5256000; // ~10 years

    public function findByToken(string $token): BranchKiosk
    {
        $kiosk = BranchKiosk::query()
            ->with(['branch', 'organization', 'activeSession'])
            ->where('token', $token)
            ->first();

        if ($kiosk === null) {
            abort(404, 'Kiosk not found.');
        }

        if (! $kiosk->is_enabled) {
            abort(403, 'This kiosk has been disabled.');
        }

        return $kiosk;
    }

    public function sessionFromRequest(Request $request, BranchKiosk $kiosk): ?KioskSession
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if ($token === null || $token === '') {
            return null;
        }

        return KioskSession::query()
            ->where('session_token', $token)
            ->where('branch_kiosk_id', $kiosk->id)
            ->whereNull('ended_at')
            ->first();
    }

    public function requireActiveSession(Request $request, BranchKiosk $kiosk): KioskSession
    {
        $session = $this->sessionFromRequest($request, $kiosk);

        if ($session === null) {
            abort(403, 'Kiosk session is not active. A business admin must start the kiosk.');
        }

        return $session;
    }

    /**
     * @return array{session: KioskSession, cookie: SymfonyCookie}
     */
    public function startSession(BranchKiosk $kiosk, User $admin, Request $request): array
    {
        $this->assertAdminForKiosk($admin, $kiosk);

        $this->endActiveSessions($kiosk, $admin, KioskActivityEvent::KioskStarted);

        $session = KioskSession::query()->create([
            'branch_kiosk_id' => $kiosk->id,
            'session_token' => Str::random(64),
            'started_by_user_id' => $admin->id,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'device_summary' => $this->deviceSummary($request->userAgent()),
            'started_at' => now(),
        ]);

        $kiosk->update(['last_started_at' => now()]);

        $this->logActivity(
            kiosk: $kiosk,
            event: KioskActivityEvent::KioskStarted,
            request: $request,
            actor: $admin,
            meta: ['session_id' => $session->id],
        );

        $cookie = Cookie::make(
            name: self::COOKIE_NAME,
            value: $session->session_token,
            minutes: self::COOKIE_MINUTES,
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'Strict',
        );

        return ['session' => $session, 'cookie' => $cookie];
    }

    public function authenticateAdmin(string $email, string $password, BranchKiosk $kiosk): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $this->assertAdminForKiosk($user, $kiosk);

        return $user;
    }

    public function authenticateAdminForExit(
        string $password,
        BranchKiosk $kiosk,
        ?KioskSession $session,
        ?string $email = null,
    ): User {
        if ($email !== null && $email !== '') {
            return $this->authenticateAdmin($email, $password, $kiosk);
        }

        if ($session === null) {
            throw ValidationException::withMessages([
                'password' => 'No active kiosk session to close.',
            ]);
        }

        $admin = $session->startedBy;

        if ($admin === null || ! Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages([
                'password' => 'Incorrect admin password.',
            ]);
        }

        $this->assertAdminForKiosk($admin, $kiosk);

        return $admin;
    }

    /**
     * @return array{cleared: bool, cookie: SymfonyCookie}
     */
    public function closeSession(BranchKiosk $kiosk, User $admin, Request $request): array
    {
        $this->assertAdminForKiosk($admin, $kiosk);

        $session = $this->sessionFromRequest($request, $kiosk);

        if ($session !== null) {
            $session->update([
                'ended_at' => now(),
                'ended_by_user_id' => $admin->id,
            ]);
        }

        $this->logActivity(
            kiosk: $kiosk,
            event: KioskActivityEvent::KioskClosed,
            request: $request,
            actor: $admin,
        );

        return [
            'cleared' => true,
            'cookie' => Cookie::forget(self::COOKIE_NAME),
        ];
    }

    public function forceLogout(BranchKiosk $kiosk, User $admin, Request $request): void
    {
        $this->assertAdminForKiosk($admin, $kiosk);
        $this->endActiveSessions($kiosk, $admin, KioskActivityEvent::ForceLogout);

        $this->logActivity(
            kiosk: $kiosk,
            event: KioskActivityEvent::ForceLogout,
            request: $request,
            actor: $admin,
        );
    }

    public function logActivity(
        BranchKiosk $kiosk,
        KioskActivityEvent $event,
        Request $request,
        ?User $staff = null,
        ?User $actor = null,
        array $meta = [],
    ): KioskActivityLog {
        return KioskActivityLog::query()->create([
            'branch_kiosk_id' => $kiosk->id,
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
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

    public function logClockEvent(
        BranchKiosk $kiosk,
        KioskActivityEvent $event,
        User $staff,
        Request $request,
        array $meta = [],
    ): void {
        $this->logActivity(
            kiosk: $kiosk,
            event: $event,
            request: $request,
            staff: $staff,
            meta: $meta,
        );
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

    private function endActiveSessions(BranchKiosk $kiosk, User $admin, KioskActivityEvent $reason): void
    {
        KioskSession::query()
            ->where('branch_kiosk_id', $kiosk->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
                'ended_by_user_id' => $admin->id,
            ]);
    }

    private function assertAdminForKiosk(User $user, BranchKiosk $kiosk): void
    {
        if (! $user->isAdmin() || (int) $user->organization_id !== (int) $kiosk->organization_id) {
            abort(403, 'Only a business admin for this organisation can manage this kiosk.');
        }
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
