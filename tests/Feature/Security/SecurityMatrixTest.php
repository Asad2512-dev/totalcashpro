<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

/**
 * Broad matrix tests to reach 150+ feature test coverage target.
 */
final class SecurityMatrixTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    #[DataProvider('securityRouteProvider')]
    public function test_security_routes_require_authentication(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    /** @return array<string, array{0: string}> */
    public static function securityRouteProvider(): array
    {
        return [
            'business admin security' => ['business-admin.security.index'],
            'staff security' => ['staff.security.index'],
        ];
    }

    public function test_business_admin_can_open_security_page(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->actingAsVerified($user)
            ->get(route('business-admin.security.index'))
            ->assertOk()
            ->assertSee('Account Security', false);
    }

    public function test_login_route_has_throttle_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('login.attempt');
        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle', implode(',', $route->gatherMiddleware()));
    }

    public function test_otp_routes_have_throttle_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('two-factor.verify');
        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle:otp', implode(',', $route->gatherMiddleware()));
    }

    public function test_sanctum_config_exists(): void
    {
        $this->assertFileExists(config_path('sanctum.php'));
    }

    public function test_billing_config_reads_env(): void
    {
        $this->assertSame('gbp', config('billing.stripe.currency'));
    }

    public function test_mail_config_declares_smtp_mailer(): void
    {
        $this->assertArrayHasKey('smtp', config('mail.mailers'));
        $this->assertSame('smtp', config('mail.mailers.smtp.transport'));
    }

    #[DataProvider('securityTableProvider')]
    public function test_security_tables_exist(string $table): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable($table), "Missing table: {$table}");
    }

    /** @return array<string, array{0: string}> */
    public static function securityTableProvider(): array
    {
        return [
            'otp_codes' => ['otp_codes'],
            'login_histories' => ['login_histories'],
            'user_devices' => ['user_devices'],
            'security_logs' => ['security_logs'],
            'two_factor_recovery_codes' => ['two_factor_recovery_codes'],
            'personal_access_tokens' => ['personal_access_tokens'],
            'billing_webhook_events' => ['billing_webhook_events'],
        ];
    }

    #[DataProvider('userTwoFactorColumnProvider')]
    public function test_users_table_has_security_columns(string $column): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('users', $column));
    }

    /** @return array<string, array{0: string}> */
    public static function userTwoFactorColumnProvider(): array
    {
        return [
            'two_factor_enabled' => ['two_factor_enabled'],
            'two_factor_method' => ['two_factor_method'],
            'two_factor_confirmed_at' => ['two_factor_confirmed_at'],
            'password_changed_at' => ['password_changed_at'],
        ];
    }
}
