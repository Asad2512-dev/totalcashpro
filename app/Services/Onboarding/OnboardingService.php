<?php

declare(strict_types=1);

namespace App\Services\Onboarding;

use App\Contracts\ServiceInterface;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\Organization;
use App\Models\User;
use App\Services\BusinessAdmin\StaffService;
use App\Services\BusinessAdmin\SupplierService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class OnboardingService implements ServiceInterface
{
    public const TOTAL_STEPS = 8;

    public function __construct(
        private readonly StaffService $staff,
        private readonly SupplierService $suppliers,
    ) {}

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
     * @param  array<string, mixed>  $data
     */
    public function updateSettings(Organization $organization, array $data): Organization
    {
        $settings = $organization->settings ?? [];
        $settings['vat_rate'] = isset($data['vat_rate']) ? (float) $data['vat_rate'] : ($settings['vat_rate'] ?? 20);

        $organization->update([
            'currency' => $data['currency'] ?? $organization->currency,
            'timezone' => $data['timezone'] ?? $organization->timezone,
            'settings' => $settings,
        ]);

        return $organization->refresh();
    }

    public function ensureCashDrawer(Organization $organization, Branch $branch, float $openingBalance): CashDrawer
    {
        $drawer = CashDrawer::query()
            ->where('branch_id', $branch->id)
            ->first();

        if ($drawer === null) {
            $drawer = CashDrawer::query()->create([
                'organization_id' => $organization->id,
                'branch_id' => $branch->id,
                'name' => $branch->name.' Drawer',
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
            ]);

            $branch->update(['cash_drawer_id' => $drawer->id]);
        } else {
            $drawer->update([
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
            ]);
        }

        return $drawer->refresh();
    }

    /**
     * @param  list<string>  $emails
     * @return list<User>
     */
    public function inviteStaff(User $admin, array $emails): array
    {
        $created = [];
        $branchId = $admin->branch_id ?? $admin->organization?->branches()->orderBy('id')->value('id');

        foreach ($emails as $email) {
            $email = strtolower(trim($email));

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (User::query()->where('email', $email)->exists()) {
                continue;
            }

            $local = Str::before($email, '@');
            $name = Str::title(str_replace(['.', '_', '-'], ' ', $local));
            $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $result = $this->staff->create($admin, [
                'name' => $name,
                'email' => $email,
                'branch_id' => $branchId,
                'pin_code' => $pin,
            ]);

            $created[] = $result['staff'];
        }

        return $created;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createFirstSupplier(User $admin, array $data): void
    {
        if (blank($data['supplier_name'] ?? null)) {
            return;
        }

        $this->suppliers->storeSupplier($admin, [
            'name' => $data['supplier_name'],
            'contact_name' => $data['supplier_contact'] ?? null,
            'email' => $data['supplier_email'] ?? null,
            'phone' => $data['supplier_phone'] ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $business
     * @param  array<string, mixed>  $branch
     * @param  array<string, mixed>  $settings
     * @param  list<string>  $staffEmails
     * @param  array<string, mixed>  $supplier
     */
    public function finalizeSetup(
        User $user,
        array $business,
        array $branch,
        array $settings,
        float $drawerOpening,
        array $staffEmails = [],
        array $supplier = [],
    ): User {
        return DB::transaction(function () use ($user, $business, $branch, $settings, $drawerOpening, $staffEmails, $supplier): User {
            $organization = $user->organization;
            $mainBranch = $organization?->branches()->orderBy('id')->first();

            if ($organization !== null) {
                $this->updateBusiness($organization, $business);
                $this->updateSettings($organization, $settings);
            }

            if ($mainBranch !== null) {
                $this->updateBranch($mainBranch, $branch);
                $this->ensureCashDrawer($organization, $mainBranch, $drawerOpening);
                $user->update(['branch_id' => $mainBranch->id]);
            }

            if ($staffEmails !== []) {
                $this->inviteStaff($user, $staffEmails);
            }

            $this->createFirstSupplier($user, $supplier);

            return $this->complete($user);
        });
    }
}
