<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RoleSlug;
use App\Events\StaffInvited;
use App\Events\StaffPasswordReset;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class StaffService implements ServiceInterface
{
    public function __construct(
        private readonly StaffRepositoryInterface $staff,
        private readonly BranchContext $branchContext,
    ) {}

    public function list(User $admin, ?string $search = null): LengthAwarePaginator
    {
        return $this->staff->paginateForOrganization(
            (int) $admin->organization_id,
            $this->branchContext->currentBranchId($admin),
            $search,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{staff: User, password: string}
     */
    public function create(User $admin, array $data): array
    {
        $this->assertUniquePin($admin, $data['pin_code'] ?? null);

        $roleId = Role::query()->where('slug', RoleSlug::Staff->value)->value('id');
        $password = $data['password'] ?? Str::password(12);

        $staff = $this->staff->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'pin_code' => $data['pin_code'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'password' => Hash::make($password),
            'role_id' => $roleId,
            'organization_id' => $admin->organization_id,
            'branch_id' => $data['branch_id'] ?? $this->branchContext->currentBranchId($admin),
            'status' => $data['status'] ?? 'active',
            'email_verified_at' => null,
        ]);

        StaffInvited::dispatch($staff, $admin, $password);

        return ['staff' => $staff, 'password' => $password];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $admin, User $staff, array $data): User
    {
        $this->assertSameOrg($admin, $staff);
        $this->assertUniquePin($admin, $data['pin_code'] ?? null, $staff->id);

        $payload = collect($data)->only([
            'name', 'email', 'phone', 'pin_code', 'hourly_rate', 'address', 'notes', 'branch_id', 'status',
        ])->all();

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        return $this->staff->update($staff->id, $payload);
    }

    public function suspend(User $admin, User $staff): User
    {
        $this->assertSameOrg($admin, $staff);

        return $this->staff->update($staff->id, ['status' => 'suspended']);
    }

    public function delete(User $admin, User $staff): void
    {
        $this->assertSameOrg($admin, $staff);
        $this->staff->delete($staff->id);
    }

    public function resetPassword(User $admin, User $staff): string
    {
        $this->assertSameOrg($admin, $staff);
        $password = Str::password(12);
        $this->staff->update($staff->id, ['password' => Hash::make($password)]);

        StaffPasswordReset::dispatch($staff->fresh(), $admin, $password);

        return $password;
    }

    private function assertSameOrg(User $admin, User $staff): void
    {
        if ((int) $staff->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }

    private function assertUniquePin(User $admin, ?string $pin, ?int $ignoreId = null): void
    {
        if ($pin === null || $pin === '') {
            return;
        }

        $exists = User::query()
            ->where('organization_id', $admin->organization_id)
            ->where('pin_code', $pin)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['pin_code' => 'This PIN is already assigned.']);
        }
    }
}
