<?php

declare(strict_types=1);

namespace App\Services\Onboarding;

use App\Contracts\ServiceInterface;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class OnboardingService implements ServiceInterface
{
    public function needsOnboarding(User $user): bool
    {
        return $user->isAdmin()
            && $user->organization_id !== null
            && $user->onboarding_completed_at === null;
    }

    public function complete(User $user): User
    {
        $user->update(['onboarding_completed_at' => now()]);

        return $user->refresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateBusiness(Organization $organization, array $data): Organization
    {
        $organization->update([
            'name' => $data['business_name'] ?? $organization->name,
            'phone' => $data['phone'] ?? $organization->phone,
            'tax_number' => $data['tax_number'] ?? $organization->tax_number,
        ]);

        return $organization->refresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateBranch(Branch $branch, array $data): Branch
    {
        $name = trim((string) ($data['name'] ?? $branch->name));

        $branch->update([
            'name' => $name,
            'city' => $data['city'] ?? $branch->city,
            'address' => $data['address'] ?? $branch->address,
            'slug' => Str::slug($name) ?: $branch->slug,
        ]);

        return $branch->refresh();
    }

    /**
     * Optional staff invites — stored as notes on org settings for now (no email blast in this phase).
     *
     * @param  list<string>  $emails
     */
    public function recordStaffInvites(Organization $organization, array $emails): void
    {
        $clean = collect($emails)
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if ($clean === []) {
            return;
        }

        $settings = $organization->settings ?? [];
        $settings['pending_staff_invites'] = $clean;

        $organization->update(['settings' => $settings]);
    }

    /**
     * @param  array<string, mixed>  $business
     * @param  array<string, mixed>  $branch
     * @param  list<string>  $staffEmails
     */
    public function finalizeSetup(User $user, array $business, array $branch, array $staffEmails = []): User
    {
        return DB::transaction(function () use ($user, $business, $branch, $staffEmails): User {
            $organization = $user->organization;
            $mainBranch = $organization?->branches()->orderBy('id')->first();

            if ($organization !== null) {
                $this->updateBusiness($organization, $business);
            }

            if ($mainBranch !== null) {
                $this->updateBranch($mainBranch, $branch);
                $user->update(['branch_id' => $mainBranch->id]);
            }

            if ($organization !== null && $staffEmails !== []) {
                $this->recordStaffInvites($organization, $staffEmails);
            }

            return $this->complete($user);
        });
    }
}
