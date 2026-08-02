<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Services\SuperAdmin\UserManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function __construct(private readonly UserManagementService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Invite User',
            'active' => 'users',
            'action' => route('super-admin.users.store'),
            'cancelRoute' => route('super-admin.users'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:active,inactive,invited'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = $this->service->create($data);
        $message = 'User created.';
        if (isset($user->temporary_password)) {
            $message .= ' Temporary password: '.$user->temporary_password;
        }

        return redirect()->route('super-admin.users.show', $user)->with('status', $message);
    }

    public function show(User $user): View
    {
        $user->load(['role', 'organization', 'branch']);

        return view('admin.crud.show', [
            'title' => $user->name,
            'active' => 'users',
            'backRoute' => route('super-admin.users'),
            'editRoute' => route('super-admin.users.edit', $user),
            'fields' => [
                ['label' => 'Name', 'value' => $user->name],
                ['label' => 'Email', 'value' => $user->email],
                ['label' => 'Phone', 'value' => $user->phone ?? '—'],
                ['label' => 'Role', 'value' => $user->role?->name ?? '—'],
                ['label' => 'Organization', 'value' => $user->organization?->name ?? '—'],
                ['label' => 'Branch', 'value' => $user->branch?->name ?? '—'],
                ['label' => 'Status', 'value' => ucfirst($user->status)],
                ['label' => 'Last login', 'value' => $user->last_login_at?->format('d M Y H:i') ?? '—'],
            ],
            'actions' => view('admin.partials.user-actions', compact('user')),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit User',
            'active' => 'users',
            'action' => route('super-admin.users.update', $user),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.users.show', $user),
            'model' => $user,
            'fields' => $this->fields($user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:active,inactive,invited'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $this->service->update($user, $data);

        return redirect()->route('super-admin.users.show', $user)->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->service->delete($user);

        return redirect()->route('super-admin.users')->with('status', 'User deleted.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $password = $this->service->resetPassword($user);

        return back()->with('status', 'Password reset. Temporary password: '.$password);
    }

    public function activate(User $user): RedirectResponse
    {
        $this->service->setStatus($user, 'active');

        return back()->with('status', 'User activated.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $this->service->setStatus($user, 'inactive');

        return back()->with('status', 'User deactivated.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?User $user = null): array
    {
        return [
            ['name' => 'name', 'value' => $user?->name],
            ['name' => 'email', 'type' => 'email', 'value' => $user?->email],
            ['name' => 'phone', 'value' => $user?->phone],
            ['name' => 'password', 'type' => 'password', 'label' => $user ? 'New password (optional)' : 'Password (optional)'],
            ['name' => 'role_id', 'type' => 'select', 'label' => 'Role', 'value' => $user?->role_id, 'options' => Role::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'organization_id', 'type' => 'select', 'label' => 'Organization', 'value' => $user?->organization_id, 'options' => ['' => '— None —'] + Organization::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'branch_id', 'type' => 'select', 'label' => 'Branch', 'value' => $user?->branch_id, 'options' => ['' => '— None —'] + Branch::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'status', 'type' => 'select', 'value' => $user?->status ?? 'active', 'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'invited' => 'Invited']],
        ];
    }
}
