<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\OrganizationStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class PruneDatabaseToCoreUsers extends Command
{
    protected $signature = 'db:prune-core-users
                            {--super-admin=admin@totalcashpro.com : Super Admin email to keep}
                            {--business-admin=ava@harbourkitchen.test : Business Admin email to keep}
                            {--staff=staff.harbour-kitchen-group@totalcashpro.test : Staff email to keep}
                            {--force : Run without confirmation (required in production)}';

    protected $description = 'Remove all data except Super Admin, one Business Admin, one Staff user, and their organization shell';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This permanently deletes almost all application data. Continue?')) {
            $this->components->warn('Aborted.');

            return self::SUCCESS;
        }

        $superAdmin = User::query()->where('email', $this->option('super-admin'))->first();
        $businessAdmin = User::query()->where('email', $this->option('business-admin'))->first();
        $staff = User::query()->where('email', $this->option('staff'))->first();

        foreach ([
            'Super Admin' => $superAdmin,
            'Business Admin' => $businessAdmin,
            'Staff' => $staff,
        ] as $label => $user) {
            if ($user === null) {
                $this->components->error("{$label} not found. Run php artisan db:seed --force first, or pass --{$label} email option.");

                return self::FAILURE;
            }
        }

        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->first();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->first();
        $superRole = Role::query()->where('slug', RoleSlug::SuperAdmin->value)->first();

        if ($superAdmin->role_id !== $superRole?->id) {
            $this->components->error('Super Admin user does not have the super_admin role.');

            return self::FAILURE;
        }

        if ($businessAdmin->role_id !== $adminRole?->id || $businessAdmin->organization_id === null) {
            $this->components->error('Business Admin must be an organization owner/admin with an organization.');

            return self::FAILURE;
        }

        if ($staff->role_id !== $staffRole?->id || $staff->organization_id === null) {
            $this->components->error('Staff user must belong to an organization.');

            return self::FAILURE;
        }

        if ((int) $businessAdmin->organization_id !== (int) $staff->organization_id) {
            $this->components->error('Business Admin and Staff must belong to the same organization.');

            return self::FAILURE;
        }

        $keepUserIds = [$superAdmin->id, $businessAdmin->id, $staff->id];
        $keepOrganizationId = (int) $businessAdmin->organization_id;
        $keepBranchId = (int) ($staff->branch_id ?? Branch::query()
            ->where('organization_id', $keepOrganizationId)
            ->orderBy('id')
            ->value('id'));

        if ($keepBranchId === 0) {
            $this->components->error('No branch found for the kept organization.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($keepUserIds, $keepOrganizationId, $keepBranchId, $businessAdmin, $staff): void {
            $this->wipeOperationalTables();

            Subscription::query()->withTrashed()->forceDelete();

            Organization::query()
                ->withTrashed()
                ->whereKeyNot($keepOrganizationId)
                ->forceDelete();

            User::query()->whereNotIn('id', $keepUserIds)->delete();

            Branch::query()
                ->where('organization_id', $keepOrganizationId)
                ->whereKeyNot($keepBranchId)
                ->delete();

            /** @var Organization $organization */
            $organization = Organization::query()->findOrFail($keepOrganizationId);

            $plan = Plan::query()->where('slug', 'professional')->first()
                ?? Plan::query()->where('is_active', true)->orderBy('sort_order')->firstOrFail();

            Subscription::query()->create([
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active,
                'starts_at' => now(),
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            $organization->update([
                'owner_user_id' => $businessAdmin->id,
                'status' => OrganizationStatus::Active,
                'trial_starts_at' => null,
                'trial_ends_at' => null,
            ]);

            $businessAdmin->update([
                'organization_id' => $keepOrganizationId,
                'branch_id' => $keepBranchId,
                'onboarding_completed_at' => $businessAdmin->onboarding_completed_at ?? now(),
                'email_verified_at' => $businessAdmin->email_verified_at ?? now(),
            ]);

            $staff->update([
                'organization_id' => $keepOrganizationId,
                'branch_id' => $keepBranchId,
                'email_verified_at' => $staff->email_verified_at ?? now(),
            ]);

            Branch::query()->whereKey($keepBranchId)->update([
                'staff_count' => 1,
                'status' => 'open',
            ]);
        });

        $this->components->info('Database pruned to core users.');
        $this->table(
            ['Role', 'Email', 'Password (unchanged)'],
            [
                ['Super Admin', $superAdmin->email, 'admin123 (if seeded)'],
                ['Business Admin', $businessAdmin->email, 'password (if seeded)'],
                ['Staff', $staff->email, 'password (if seeded)'],
            ],
        );

        return self::SUCCESS;
    }

    private function wipeOperationalTables(): void
    {
        $tables = [
            'support_ticket_replies',
            'support_tickets',
            'notifications',
            'attendance_breaks',
            'attendance_logs',
            'cash_ups',
            'inventory_counts',
            'inventory_items',
            'inventory_categories',
            'supplier_invoices',
            'suppliers',
            'wages',
            'rota_shifts',
            'rota_group_user',
            'rota_sections',
            'rota_groups',
            'payments',
            'invoices',
            'subscription_histories',
            'discounts',
            'coupons',
            'access_requests',
            'contact_messages',
            'announcements',
            'activity_logs',
            'audit_logs',
            'password_reset_tokens',
            'sessions',
            'jobs',
            'job_batches',
            'failed_jobs',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
