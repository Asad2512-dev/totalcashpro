<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class DeviceSessionService
{
    public function __construct(
        private readonly UserAgentParser $userAgentParser,
    ) {}

    public function register(User $user, Request $request): UserDevice
    {
        $parsed = $this->userAgentParser->parse($request->userAgent());
        $sessionId = $request->session()->getId();

        UserDevice::query()
            ->where('user_id', $user->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        return UserDevice::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ],
            [
                'device_name' => $parsed['device'].' · '.$parsed['browser'],
                'browser' => $parsed['browser'],
                'operating_system' => $parsed['os'],
                'ip_address' => $request->ip(),
                'is_current' => true,
                'last_active_at' => now(),
                'logged_out_at' => null,
            ],
        );
    }

    public function touch(User $user, Request $request): void
    {
        UserDevice::query()
            ->where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->whereNull('logged_out_at')
            ->update(['last_active_at' => now()]);
    }

    public function markTrusted(UserDevice $device): void
    {
        $device->update(['is_trusted' => true]);
    }

    public function logoutDevice(UserDevice $device): void
    {
        $device->update([
            'logged_out_at' => now(),
            'is_current' => false,
        ]);

        if ($device->session_id) {
            DB::table('sessions')->where('id', $device->session_id)->delete();
        }
    }

    public function logoutAllExceptCurrent(User $user, Request $request): int
    {
        $currentSession = $request->session()->getId();

        $devices = UserDevice::query()
            ->where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->where('session_id', '!=', $currentSession)
            ->get();

        foreach ($devices as $device) {
            $this->logoutDevice($device);
        }

        return $devices->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, UserDevice>
     */
    public function activeForUser(User $user)
    {
        return UserDevice::query()
            ->where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->latest('last_active_at')
            ->get();
    }
}
