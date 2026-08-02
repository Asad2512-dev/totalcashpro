<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\DTOs\OrganizationData;
use App\Enums\OrganizationStatus;
use App\Enums\RoleSlug;
use App\Mail\AccessCredentialsMail;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

final class OrganizationService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * Create a business with exactly one owner account and email login credentials.
     *
     * @param  array<string, mixed>  $data
     * @return array{organization: Organization, owner: User, password: string}
     */
    public function createWithOwner(array $data): array
    {
        $ownerName = trim((string) ($data['owner_name'] ?? ''));
        $ownerEmail = strtolower(trim((string) ($data['owner_email'] ?? '')));

        if ($ownerName === '' || $ownerEmail === '') {
            throw ValidationException::withMessages([
                'owner_name' => 'Owner name is required.',
                'owner_email' => 'Owner email is required.',
            ]);
        }

        if (User::query()->where('email', $ownerEmail)->exists()) {
            throw ValidationException::withMessages([
                'owner_email' => 'A user with this email already exists. Use a unique owner email.',
            ]);
        }

        unset($data['owner_name'], $data['owner_email'], $data['owner_user_id']);

        if (empty($data['email'])) {
            $data['email'] = $ownerEmail;
        }

        return DB::transaction(function () use ($data, $ownerName, $ownerEmail): array {
            $payload = OrganizationData::fromValidated($data)->toArray();
            $payload['slug'] = $this->uniqueSlug($payload['slug'] ?? $payload['name']);
            $payload['status'] = $payload['status'] ?? OrganizationStatus::Pending->value;

            $organization = Organization::query()->create($payload);

            $password = Str::password(12);
            $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();

            $owner = User::query()->create([
                'name' => $ownerName,
                'email' => $ownerEmail,
                'phone' => $organization->phone,
                'password' => Hash::make($password),
                'role_id' => $role->id,
                'organization_id' => $organization->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $organization->update(['owner_user_id' => $owner->id]);

            $this->logAdminAction(
                'organization.created',
                'Business created with owner: '.$organization->name,
                $organization,
                null,
                [
                    'organization_id' => $organization->id,
                    'owner_user_id' => $owner->id,
                    'owner_email' => $owner->email,
                ],
            );

            Mail::to($owner->email)->send(new AccessCredentialsMail($owner, $password, $organization));

            return [
                'organization' => $organization->refresh(),
                'owner' => $owner,
                'password' => $password,
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Organization
    {
        return $this->createWithOwner($data)['organization'];
    }

    /**
     * Reset the single owner password and email fresh credentials.
     *
     * @return array{owner: User, password: string}
     */
    public function sendOwnerCredentials(Organization $organization): array
    {
        $owner = $organization->owner;

        if (! $owner) {
            throw new RuntimeException('This business has no owner account. Edit the business and assign an owner first.');
        }

        $password = Str::password(12);
        $owner->update(['password' => Hash::make($password)]);

        Mail::to($owner->email)->send(new AccessCredentialsMail($owner, $password, $organization));

        $this->logAdminAction(
            'organization.credentials_sent',
            'Login credentials sent to owner of '.$organization->name,
            $organization,
            null,
            ['owner_user_id' => $owner->id, 'owner_email' => $owner->email],
        );

        return [
            'owner' => $owner->refresh(),
            'password' => $password,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Organization $organization, array $data): Organization
    {
        $old = $organization->toArray();

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name'], $organization->id);
        } elseif (! empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $organization->id);
        }

        // Keep a single owner: if owner changes, link that user to this organisation.
        if (! empty($data['owner_user_id'])) {
            $ownerId = (int) $data['owner_user_id'];
            User::query()->whereKey($ownerId)->update(['organization_id' => $organization->id]);
        }

        $organization->update($data);

        $this->logAdminAction(
            'organization.updated',
            'Business updated: '.$organization->name,
            $organization,
            $old,
            $organization->fresh()?->toArray(),
        );

        return $organization->refresh();
    }

    public function setStatus(Organization $organization, OrganizationStatus $status): Organization
    {
        $old = ['status' => $organization->status?->value ?? $organization->status];
        $organization->update(['status' => $status]);

        $this->logAdminAction(
            'organization.status_changed',
            'Business '.$organization->name.' marked '.$status->label(),
            $organization,
            $old,
            ['status' => $status->value],
        );

        return $organization->refresh();
    }

    public function delete(Organization $organization): void
    {
        $snapshot = $organization->toArray();
        $name = $organization->name;
        $organization->delete();

        $this->logAdminAction(
            'organization.deleted',
            'Business deleted: '.$name,
            null,
            $snapshot,
            null,
        );
    }

    /**
     * @param  list<int|string>  $ids
     */
    public function bulkSuspend(array $ids): int
    {
        $count = 0;

        Organization::query()->whereIn('id', $ids)->get()->each(function (Organization $organization) use (&$count): void {
            $this->setStatus($organization, OrganizationStatus::Suspended);
            $count++;
        });

        return $count;
    }

    /**
     * @param  list<int|string>  $ids
     */
    public function bulkDelete(array $ids): int
    {
        $count = 0;

        Organization::query()->whereIn('id', $ids)->get()->each(function (Organization $organization) use (&$count): void {
            $this->delete($organization);
            $count++;
        });

        return $count;
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'business';
        $slug = $base;
        $i = 1;

        while (
            Organization::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        if ($slug === '') {
            throw ValidationException::withMessages(['slug' => 'Unable to generate a valid slug.']);
        }

        return $slug;
    }
}
