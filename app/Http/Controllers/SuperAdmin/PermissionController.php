<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\SuperAdmin\RolePermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PermissionController extends Controller
{
    public function __construct(private readonly RolePermissionService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Permission',
            'active' => 'permissions',
            'action' => route('super-admin.permissions.store'),
            'cancelRoute' => route('super-admin.permissions'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->createPermission($this->validated($request));

        return redirect()->route('super-admin.permissions')->with('status', 'Permission created.');
    }

    public function edit(Permission $permission): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Permission',
            'active' => 'permissions',
            'action' => route('super-admin.permissions.update', $permission),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.permissions'),
            'model' => $permission,
            'fields' => $this->fields($permission),
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $this->service->updatePermission($permission, $this->validated($request, $permission));

        return redirect()->route('super-admin.permissions')->with('status', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->service->deletePermission($permission);

        return redirect()->route('super-admin.permissions')->with('status', 'Permission deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Permission $permission = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug,'.($permission?->id ?? 'NULL')],
            'group' => ['required', 'string', 'max:100'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Permission $permission = null): array
    {
        return [
            ['name' => 'name', 'value' => $permission?->name],
            ['name' => 'slug', 'label' => 'Slug (e.g. businesses.manage)', 'value' => $permission?->slug],
            ['name' => 'group', 'value' => $permission?->group ?? 'general'],
        ];
    }
}
