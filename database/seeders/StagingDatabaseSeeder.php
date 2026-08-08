<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Staging-only seed: system tables + one realistic Harbour Kitchen business.
 * Does NOT seed multi-business demo data.
 */
final class StagingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            PlanSeeder::class,
            SettingsSeeder::class,
            CmsSeeder::class,
            \Database\Seeders\HarbourKitchen\HarbourKitchenRealisticSeeder::class,
        ]);
    }
}
