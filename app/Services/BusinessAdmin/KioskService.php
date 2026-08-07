<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Branch;
use App\Models\User;
use App\Services\Logging\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

final class KioskService implements ServiceInterface
{
    private const SESSION_ACTIVE = 'attendance_kiosk.active';

    private const SESSION_BRANCH = 'attendance_kiosk.branch_id';

    private const SESSION_OPENED = 'attendance_kiosk.opened_at';

    private const SESSION_ACTIVITY = 'attendance_kiosk.last_activity';

    public function __construct(private readonly ActivityLogger $activity) {}

    public function isActive(): bool
    {
        return (bool) Session::get(self::SESSION_ACTIVE, false);
    }

    public function branchId(): ?int
    {
        $id = Session::get(self::SESSION_BRANCH);

        return $id !== null ? (int) $id : null;
    }

    public function touchActivity(): void
    {
        if ($this->isActive()) {
            Session::put(self::SESSION_ACTIVITY, now()->timestamp);
        }
    }

    public function activate(User $admin, Branch $branch): void
    {
        $this->assertAdminOwnsBranch($admin, $branch);

        Session::put([
            self::SESSION_ACTIVE => true,
            self::SESSION_BRANCH => $branch->id,
            self::SESSION_OPENED => now()->timestamp,
            self::SESSION_ACTIVITY => now()->timestamp,
        ]);

        $this->log($admin, 'kiosk.opened', 'Attendance kiosk opened', [
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
        ]);
    }

    public function deactivate(User $admin, string $password): void
    {
        if (! Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages(['password' => 'Incorrect password.']);
        }

        $branchId = $this->branchId();

        Session::forget([
            self::SESSION_ACTIVE,
            self::SESSION_BRANCH,
            self::SESSION_OPENED,
            self::SESSION_ACTIVITY,
        ]);

        $this->log($admin, 'kiosk.closed', 'Attendance kiosk closed', [
            'branch_id' => $branchId,
        ]);
    }

    /**
     * @return array{welcome_message: string, session_timeout_minutes: int, success_display_seconds: int, show_photos: bool}
     */
    public function settings(User $admin): array
    {
        $stored = $admin->organization?->settings['kiosk'] ?? [];

        return [
            'welcome_message' => (string) ($stored['welcome_message'] ?? 'Welcome — please enter your PIN to clock in or out.'),
            'session_timeout_minutes' => max(30, (int) ($stored['session_timeout_minutes'] ?? 480)),
            'success_display_seconds' => max(2, min(10, (int) ($stored['success_display_seconds'] ?? 4))),
            'show_photos' => (bool) ($stored['show_photos'] ?? true),
        ];
    }

    /**
     * @param  array{welcome_message?: string, session_timeout_minutes?: int, success_display_seconds?: int, show_photos?: bool}  $input
     */
    public function updateSettings(User $admin, array $input): void
    {
        $organization = $admin->organization;

        if ($organization === null) {
            abort(403);
        }

        $settings = $organization->settings ?? [];
        $settings['kiosk'] = [
            'welcome_message' => trim((string) ($input['welcome_message'] ?? '')) ?: 'Welcome — please enter your PIN to clock in or out.',
            'session_timeout_minutes' => max(30, (int) ($input['session_timeout_minutes'] ?? 480)),
            'success_display_seconds' => max(2, min(10, (int) ($input['success_display_seconds'] ?? 4))),
            'show_photos' => (bool) ($input['show_photos'] ?? true),
        ];

        $organization->update(['settings' => $settings]);
    }

    public function sessionExpired(User $admin): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $last = (int) Session::get(self::SESSION_ACTIVITY, 0);
        $timeout = $this->settings($admin)['session_timeout_minutes'] * 60;

        return $last > 0 && (now()->timestamp - $last) > $timeout;
    }

    public function requireActiveBranch(User $admin): Branch
    {
        if (! $this->isActive()) {
            abort(403, 'Kiosk mode is not active.');
        }

        if ($this->sessionExpired($admin)) {
            Session::forget([
                self::SESSION_ACTIVE,
                self::SESSION_BRANCH,
                self::SESSION_OPENED,
                self::SESSION_ACTIVITY,
            ]);
            abort(403, 'Kiosk session expired.');
        }

        $branchId = $this->branchId();

        if ($branchId === null) {
            abort(403, 'No kiosk branch selected.');
        }

        $branch = Branch::query()
            ->where('organization_id', $admin->organization_id)
            ->whereKey($branchId)
            ->first();

        if ($branch === null) {
            abort(403, 'Invalid kiosk branch.');
        }

        $this->touchActivity();

        return $branch;
    }

    public function logPinFailure(User $admin, ?int $branchId): void
    {
        $this->log($admin, 'kiosk.pin_failed', 'Failed PIN attempt on attendance kiosk', [
            'branch_id' => $branchId,
        ]);
    }

    public function logClockEvent(User $admin, User $staff, string $action, int $branchId): void
    {
        $this->log($admin, 'kiosk.'.$action, $staff->name.' performed '.$action.' via kiosk', [
            'branch_id' => $branchId,
            'staff_id' => $staff->id,
            'staff_name' => $staff->name,
        ], $staff);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function log(User $admin, string $event, string $description, array $properties = [], ?User $subject = null): void
    {
        $this->activity->log(
            event: $event,
            description: $description,
            actor: $admin,
            subject: $subject,
            properties: $properties,
        );
    }

    private function assertAdminOwnsBranch(User $admin, Branch $branch): void
    {
        if ((int) $branch->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }
}
