<?php

declare(strict_types=1);

namespace App\Support\Security;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class StaffPinHasher
{
    public static function hash(string $pin): string
    {
        return Hash::make($pin);
    }

    public static function verify(string $pin, ?string $pinHash): bool
    {
        if ($pinHash === null || $pinHash === '') {
            return false;
        }

        return Hash::check($pin, $pinHash);
    }

    public static function findStaffByPin(int $organizationId, string $pin, ?int $branchId = null): ?User
    {
        $query = User::query()
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->whereNotNull('pin_hash');

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        foreach ($query->get() as $user) {
            if (self::verify($pin, $user->pin_hash)) {
                return $user;
            }
        }

        return null;
    }

    public static function pinInUse(int $organizationId, string $pin, ?int $ignoreUserId = null): bool
    {
        $query = User::query()
            ->where('organization_id', $organizationId)
            ->whereNotNull('pin_hash');

        if ($ignoreUserId !== null) {
            $query->where('id', '!=', $ignoreUserId);
        }

        foreach ($query->get() as $user) {
            if (self::verify($pin, $user->pin_hash)) {
                return true;
            }
        }

        return false;
    }
}
