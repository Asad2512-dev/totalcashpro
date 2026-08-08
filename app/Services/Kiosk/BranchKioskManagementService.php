<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\KioskActivityEvent;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\KioskActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class BranchKioskManagementService implements ServiceInterface
{
    public function __construct(private readonly SmartKioskService $kiosk) {}

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, BranchKiosk>
     */
    public function listForOrganization(User $admin)
    {
        return BranchKiosk::query()
            ->with(['branch', 'activeSession.startedBy'])
            ->where('organization_id', $admin->organization_id)
            ->orderBy('name')
            ->get();
    }

    public function create(User $admin, Branch $branch, ?string $name = null): BranchKiosk
    {
        $this->assertBranch($admin, $branch);

        return BranchKiosk::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => $name !== null && trim($name) !== '' ? trim($name) : $this->defaultKioskName($branch),
            'token' => $this->generateToken(),
            'welcome_message' => 'Enter your 4-digit PIN',
            'settings' => app(KioskConfigurationService::class)->defaults(),
        ]);
    }

    public function update(User $admin, BranchKiosk $kiosk, array $data): BranchKiosk
    {
        $this->assertKiosk($admin, $kiosk);

        $settings = $kiosk->settings ?? app(KioskConfigurationService::class)->defaults();

        if (isset($data['settings']) && is_array($data['settings'])) {
            $settings = array_replace_recursive($settings, $data['settings']);
        }

        $kiosk->update([
            'name' => trim((string) ($data['name'] ?? $kiosk->name)),
            'description' => trim((string) ($data['description'] ?? $kiosk->description ?? '')),
            'welcome_message' => trim((string) ($data['welcome_message'] ?? $kiosk->welcome_message)),
            'show_photos' => (bool) ($data['show_photos'] ?? $kiosk->show_photos),
            'settings' => $settings,
        ]);

        return $kiosk->fresh(['branch', 'activeSession']);
    }

    public function setEnabled(User $admin, BranchKiosk $kiosk, bool $enabled): BranchKiosk
    {
        $this->assertKiosk($admin, $kiosk);
        $kiosk->update(['is_enabled' => $enabled]);

        return $kiosk;
    }

    public function regenerateToken(User $admin, BranchKiosk $kiosk, Request $request): BranchKiosk
    {
        $this->assertKiosk($admin, $kiosk);

        $kiosk->update(['token' => $this->generateToken()]);

        $this->kiosk->forceLogout($kiosk, $admin, $request);

        $this->kiosk->logActivity(
            kiosk: $kiosk,
            event: KioskActivityEvent::TokenRegenerated,
            request: $request,
            actor: $admin,
        );

        return $kiosk->fresh();
    }

    public function reset(User $admin, BranchKiosk $kiosk, Request $request): BranchKiosk
    {
        $this->assertKiosk($admin, $kiosk);

        $this->kiosk->forceLogout($kiosk, $admin, $request);

        $kiosk->update([
            'welcome_message' => 'Welcome — enter your PIN to clock in or out.',
            'show_photos' => true,
            'is_enabled' => true,
        ]);

        $this->kiosk->logActivity(
            kiosk: $kiosk,
            event: KioskActivityEvent::KioskReset,
            request: $request,
            actor: $admin,
        );

        return $kiosk->fresh(['branch', 'activeSession']);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<KioskActivityLog>
     */
    public function activity(User $admin, BranchKiosk $kiosk, int $perPage = 30)
    {
        $this->assertKiosk($admin, $kiosk);

        return KioskActivityLog::query()
            ->with(['staff', 'actor'])
            ->where('branch_kiosk_id', $kiosk->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Branch>
     */
    public function branchesForOrganization(User $admin)
    {
        return Branch::query()
            ->where('organization_id', $admin->organization_id)
            ->withCount('kiosks')
            ->orderBy('name')
            ->get();
    }

    private function defaultKioskName(Branch $branch): string
    {
        $count = BranchKiosk::query()->where('branch_id', $branch->id)->count();

        return $count === 0
            ? $branch->name.' Kiosk'
            : $branch->name.' Kiosk '.($count + 1);
    }

    private function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (BranchKiosk::query()->where('token', $token)->exists());

        return $token;
    }

    private function assertKiosk(User $admin, BranchKiosk $kiosk): void
    {
        if ((int) $kiosk->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }

    private function assertBranch(User $admin, Branch $branch): void
    {
        if ((int) $branch->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }
}
