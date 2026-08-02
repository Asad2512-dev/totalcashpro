<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->where('slug', RoleSlug::SuperAdmin->value)->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'admin@totalcashpro.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $role->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );
    }
}
