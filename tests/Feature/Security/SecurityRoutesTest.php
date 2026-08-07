<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class SecurityRoutesTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    #[DataProvider('throttledAuthRouteProvider')]
    public function test_auth_routes_define_throttle(string $routeName): void
    {
        $route = app('router')->getRoutes()->getByName($routeName);
        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle', implode(',', $route->gatherMiddleware()));
    }

    /** @return array<string, array{0: string}> */
    public static function throttledAuthRouteProvider(): array
    {
        return [
            'login' => ['login.attempt'],
            'password email' => ['password.email'],
            'verification send' => ['verification.send'],
            'two factor verify' => ['two-factor.verify'],
            'two factor resend' => ['two-factor.resend'],
            'security otp enable' => ['business-admin.security.two-factor.enable'],
        ];
    }

    public function test_super_admin_can_open_security_page(): void
    {
        $this->seed([RolePermissionSeeder::class, SuperAdminSeeder::class]);
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAsVerified($admin)
            ->get(route('super-admin.security.index'))
            ->assertOk()
            ->assertSee('Account Security', false);
    }

    public function test_staff_can_open_security_page(): void
    {
        $this->seedRolesAndPlans();
        $role = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $admin = $this->makeBusinessAdmin();

        $staff = User::query()->create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $admin->organization_id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAsVerified($staff)
            ->get(route('staff.security.index'))
            ->assertOk();
    }

    public function test_two_factor_challenge_redirects_without_session(): void
    {
        $this->get(route('two-factor.challenge'))->assertRedirect(route('login'));
    }

    public function test_stripe_webhook_route_exists(): void
    {
        $this->assertNotNull(app('router')->getRoutes()->getByName('webhooks.stripe'));
    }

    public function test_user_model_uses_sanctum_trait(): void
    {
        $this->assertTrue(in_array(\Laravel\Sanctum\HasApiTokens::class, class_uses(User::class), true));
    }

    public function test_welcome_mail_class_exists(): void
    {
        $this->assertTrue(class_exists(\App\Mail\WelcomeMail::class));
    }

    public function test_send_welcome_email_listener_is_queued(): void
    {
        $reflection = new \ReflectionClass(\App\Listeners\SendWelcomeEmail::class);
        $this->assertTrue($reflection->implementsInterface(\Illuminate\Contracts\Queue\ShouldQueue::class));
    }

    public function test_otp_expiry_constant_is_ten_minutes(): void
    {
        $this->assertSame(10, \App\Services\Security\OtpService::EXPIRY_MINUTES);
    }

    public function test_otp_code_length_is_six(): void
    {
        $this->assertSame(6, \App\Services\Security\OtpService::CODE_LENGTH);
    }

    public function test_security_log_event_labels_are_human_readable(): void
    {
        $label = \App\Enums\SecurityLogEvent::LoginSuccess->label();
        $this->assertSame('Login Success', $label);
    }

    public function test_two_factor_method_supports_totp_for_future(): void
    {
        $this->assertSame('totp', \App\Enums\TwoFactorMethod::Totp->value);
    }
}
