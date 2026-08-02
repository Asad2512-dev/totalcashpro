<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

final class RolePermissionService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $permissionIds
     */
    public function createRole(array $data, array $permissionIds = []): Role
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['name'], '_');
        $role = Role::query()->create($data);
        $role->permissions()->sync($permissionIds);
        $this->logAdminAction('role.created', 'Role created: '.$role->name, $role);

        return $role;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $permissionIds
     */
    public function updateRole(Role $role, array $data, array $permissionIds = []): Role
    {
        if ($role->is_protected && isset($data['slug']) && $data['slug'] !== $role->slug) {
            unset($data['slug']);
        }

        $role->update($data);
        $role->permissions()->sync($permissionIds);
        $this->logAdminAction('role.updated', 'Role updated: '.$role->name, $role);

        return $role->refresh();
    }

    public function deleteRole(Role $role): void
    {
        if ($role->is_protected) {
            abort(422, 'Protected roles cannot be deleted.');
        }

        $snapshot = $role->toArray();
        $name = $role->name;
        $role->delete();
        $this->logAdminAction('role.deleted', 'Role deleted: '.$name, null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPermission(array $data): Permission
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name'], '.');
        $permission = Permission::query()->create($data);
        $this->logAdminAction('permission.created', 'Permission created: '.$permission->slug, $permission);

        return $permission;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $old = $permission->toArray();
        $permission->update($data);
        $this->logAdminAction('permission.updated', 'Permission updated: '.$permission->slug, $permission, $old, $permission->fresh()?->toArray());

        return $permission->refresh();
    }

    public function deletePermission(Permission $permission): void
    {
        $snapshot = $permission->toArray();
        $slug = $permission->slug;
        $permission->roles()->detach();
        $permission->delete();
        $this->logAdminAction('permission.deleted', 'Permission deleted: '.$slug, null, $snapshot);
    }
}
