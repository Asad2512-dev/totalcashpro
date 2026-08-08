<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleSlug: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Staff = 'staff';
    case Rider = 'rider';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::Rider => 'Rider',
        };
    }
}
