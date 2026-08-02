<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserManagementService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        $plain = $data['password'] ?? Str::password(12);
        $data['password'] = Hash::make($plain);
        unset($data['password_confirmation']);

        $user = User::query()->create($data);

        $this->logAdminAction('user.created', 'User created: '.$user->email, $user, null, [
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);

        $user->temporary_password = $plain;

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $old = $user->only(['name', 'email', 'role_id', 'organization_id', 'branch_id', 'status', 'phone']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation']);

        $user->update($data);

        $this->logAdminAction('user.updated', 'User updated: '.$user->email, $user, $old, $user->fresh()?->only(array_keys($old)));

        return $user->refresh();
    }

    public function resetPassword(User $user, ?string $password = null): string
    {
        $plain = $password ?: Str::password(12);
        $user->update(['password' => Hash::make($plain)]);

        $this->logAdminAction('user.password_reset', 'Password reset for '.$user->email, $user);

        return $plain;
    }

    public function setStatus(User $user, string $status): User
    {
        $old = ['status' => $user->status];
        $user->update(['status' => $status]);
        $this->logAdminAction('user.status_changed', 'User '.$user->email.' marked '.$status, $user, $old, ['status' => $status]);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        if ($user->isSuperAdmin() && User::query()->where('role_id', $user->role_id)->count() <= 1) {
            abort(422, 'Cannot delete the last Super Admin.');
        }

        $email = $user->email;
        $snapshot = $user->toArray();
        $user->delete();
        $this->logAdminAction('user.deleted', 'User deleted: '.$email, null, $snapshot);
    }
}
