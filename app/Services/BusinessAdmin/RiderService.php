<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RoleSlug;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class RiderService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    /**
     * @return Collection<int, Rider>
     */
    public function list(User $user): Collection
    {
        return Rider::query()
            ->with('user')
            ->where('organization_id', $user->organization_id)
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $admin, array $data): Rider
    {
        $role = \App\Models\Role::query()->where('slug', RoleSlug::Rider->value)->firstOrFail();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role_id' => $role->id,
            'organization_id' => $admin->organization_id,
            'branch_id' => $data['branch_id'] ?? null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return Rider::query()->create([
            'organization_id' => $admin->organization_id,
            'user_id' => $user->id,
            'branch_ids' => $data['branch_ids'] ?? ($data['branch_id'] ? [(int) $data['branch_id']] : null),
            'phone' => $data['phone'] ?? null,
            'vehicle' => $data['vehicle'] ?? null,
            'is_active' => true,
        ]);
    }

    public function findForUser(User $user): ?Rider
    {
        return Rider::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
    }

    public function toggleActive(User $admin, Rider $rider, bool $active): Rider
    {
        if ((int) $rider->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        $rider->update(['is_active' => $active]);

        return $rider->refresh();
    }
}
