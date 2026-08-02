<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\SuperAdmin\RolePermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class RoleController extends Controller
{
    public function __construct(private readonly RolePermissionService $service) {}

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => null,
            'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group'),
            'selected' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $this->service->createRole($data, $data['permissions'] ?? []);

        return redirect()->route('super-admin.roles')->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', [
            'role' => $role,
            'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group'),
            'selected' => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $this->service->updateRole($role, $data, $data['permissions'] ?? []);

        return redirect()->route('super-admin.roles')->with('status', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->service->deleteRole($role);

        return redirect()->route('super-admin.roles')->with('status', 'Role deleted.');
    }
}
