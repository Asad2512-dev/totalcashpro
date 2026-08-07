<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CmsSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class V1SuperAdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            PlanSeeder::class,
            SettingsSeeder::class,
            CmsSeeder::class,
        ]);
    }

    #[DataProvider('superAdminGetRoutes')]
    public function test_super_admin_get_route_is_accessible(string $routeName): void
    {
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route($routeName))
            ->assertOk();
    }

    public static function superAdminGetRoutes(): array
    {
        $pages = [
            'dashboard', 'search', 'businesses', 'organizations', 'business-requests',
            'branches', 'users', 'plans', 'subscriptions', 'coupons', 'discounts',
            'trials', 'payments', 'revenue', 'analytics', 'support', 'announcements',
            'contact-messages', 'roles', 'permissions', 'settings', 'email-templates',
            'email-queue', 'media', 'profile', 'notifications',
        ];

        return array_map(fn (string $page) => ["super-admin.{$page}"], $pages);
    }
}
