<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'Overview'],
            ['name' => 'Manage Businesses', 'slug' => 'businesses.manage', 'group' => 'Customers'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'group' => 'Customers'],
            ['name' => 'Manage Plans', 'slug' => 'plans.manage', 'group' => 'Billing'],
            ['name' => 'Manage Subscriptions', 'slug' => 'subscriptions.manage', 'group' => 'Billing'],
            ['name' => 'Manage Coupons', 'slug' => 'coupons.manage', 'group' => 'Billing'],
            ['name' => 'Manage CMS', 'slug' => 'cms.manage', 'group' => 'CMS'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'System'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'group' => 'System'],
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'group' => 'System'],
        ];

        $permissionIds = [];

        foreach ($permissions as $permission) {
            $permissionIds[] = Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            )->id;
        }

        $superAdmin = Role::query()->updateOrCreate(
            ['slug' => RoleSlug::SuperAdmin->value],
            [
                'name' => RoleSlug::SuperAdmin->label(),
                'description' => 'Full platform access for TotalCashPro operators.',
                'is_protected' => true,
            ],
        );

        Role::query()->updateOrCreate(
            ['slug' => RoleSlug::Admin->value],
            [
                'name' => RoleSlug::Admin->label(),
                'description' => 'Business admin role (Phase 2+).',
                'is_protected' => true,
            ],
        );

        Role::query()->updateOrCreate(
            ['slug' => RoleSlug::Staff->value],
            [
                'name' => RoleSlug::Staff->label(),
                'description' => 'Staff role (Phase 2+).',
                'is_protected' => true,
            ],
        );

        $superAdmin->permissions()->sync($permissionIds);
    }
}
