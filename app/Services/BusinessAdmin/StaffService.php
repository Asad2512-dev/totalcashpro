<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RoleSlug;
use App\Events\StaffInvited;
use App\Events\StaffPasswordReset;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Support\Security\StaffPinHasher;
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
        $this->assertBranchInOrganization($admin, $data['branch_id'] ?? null);
        $this->assertUniquePin($admin, $data['pin_code'] ?? null);

        $roleId = Role::query()->where('slug', RoleSlug::Staff->value)->value('id');
        $password = $data['password'] ?? Str::password(12);

        $staff = $this->staff->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'pin_hash' => $this->hashPin($data['pin_code'] ?? null),
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
        $this->assertBranchInOrganization($admin, $data['branch_id'] ?? null);
        $this->assertUniquePin($admin, $data['pin_code'] ?? null, $staff->id);

        $payload = collect($data)->only([
            'name', 'email', 'phone', 'hourly_rate', 'address', 'notes', 'branch_id', 'status',
        ])->all();

        if (array_key_exists('pin_code', $data) && $data['pin_code'] !== null && $data['pin_code'] !== '') {
            $payload['pin_hash'] = $this->hashPin($data['pin_code']);
        }

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

    public function resetPin(User $admin, User $staff): string
    {
        $this->assertSameOrg($admin, $staff);

        do {
            $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (StaffPinHasher::pinInUse((int) $admin->organization_id, $pin, $staff->id));

        $this->staff->update($staff->id, ['pin_hash' => StaffPinHasher::hash($pin)]);

        return $pin;
    }

    private function assertSameOrg(User $admin, User $staff): void
    {
        if ((int) $staff->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }

    private function assertBranchInOrganization(User $admin, mixed $branchId): void
    {
        if ($branchId === null || $branchId === '') {
            return;
        }

        $exists = Branch::query()
            ->where('organization_id', $admin->organization_id)
            ->whereKey($branchId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages(['branch_id' => 'The selected branch is not valid for your organisation.']);
        }
    }

    private function assertUniquePin(User $admin, ?string $pin, ?int $ignoreId = null): void
    {
        if ($pin === null || $pin === '') {
            return;
        }

        if (StaffPinHasher::pinInUse((int) $admin->organization_id, $pin, $ignoreId)) {
            throw ValidationException::withMessages(['pin_code' => 'This PIN is already assigned.']);
        }
    }

    private function hashPin(?string $pin): ?string
    {
        if ($pin === null || $pin === '') {
            return null;
        }

        return StaffPinHasher::hash($pin);
    }
}
