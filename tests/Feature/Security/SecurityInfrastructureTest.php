<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Ensures Milestone 4 security artifacts exist. */
final class SecurityInfrastructureTest extends TestCase
{
    #[DataProvider('serviceClassProvider')]
    public function test_security_service_class_exists(string $class): void
    {
        $this->assertTrue(class_exists($class), "Missing class: {$class}");
    }

    /** @return array<string, array{0: string}> */
    public static function serviceClassProvider(): array
    {
        return [
            'otp' => [\App\Services\Security\OtpService::class],
            'two factor' => [\App\Services\Security\TwoFactorService::class],
            'login history' => [\App\Services\Security\LoginHistoryService::class],
            'device session' => [\App\Services\Security\DeviceSessionService::class],
            'security log' => [\App\Services\Security\SecurityLogService::class],
            'password' => [\App\Services\Security\PasswordService::class],
            'user agent' => [\App\Services\Security\UserAgentParser::class],
            'stripe webhook' => [\App\Services\Billing\StripeWebhookService::class],
        ];
    }

    #[DataProvider('notificationClassProvider')]
    public function test_notification_class_exists(string $class): void
    {
        $this->assertTrue(class_exists($class));
    }

    /** @return array<string, array{0: string}> */
    public static function notificationClassProvider(): array
    {
        return [
            'otp' => [\App\Notifications\OtpCodeNotification::class],
            'verify email' => [\App\Notifications\VerifyEmailNotification::class],
            'reset password' => [\App\Notifications\ResetPasswordNotification::class],
            'leave status' => [\App\Notifications\LeaveStatusNotification::class],
            'shift swap' => [\App\Notifications\ShiftSwapStatusNotification::class],
            'staff invitation' => [\App\Notifications\StaffInvitationNotification::class],
            'trial ending' => [\App\Notifications\TrialEndingNotification::class],
            'subscription expired' => [\App\Notifications\SubscriptionExpiredNotification::class],
        ];
    }

    #[DataProvider('emailViewProvider')]
    public function test_email_blade_view_exists(string $view): void
    {
        $this->assertTrue(view()->exists($view), "Missing view: {$view}");
    }

    /** @return array<string, array{0: string}> */
    public static function emailViewProvider(): array
    {
        return [
            'welcome' => ['emails.welcome'],
            'otp' => ['emails.otp-code'],
            'password reset' => ['emails.password-reset'],
            'verify email' => ['emails.verify-email'],
            'leave status' => ['emails.leave-status'],
            'shift swap' => ['emails.shift-swap-status'],
            'trial ending' => ['emails.trial-ending'],
            'subscription expired' => ['emails.subscription-expired'],
            'staff invitation' => ['emails.staff-invitation'],
        ];
    }

    #[DataProvider('enumProvider')]
    public function test_security_enum_exists(string $class): void
    {
        $this->assertTrue(enum_exists($class));
    }

    /** @return array<string, array{0: string}> */
    public static function enumProvider(): array
    {
        return [
            'otp purpose' => [\App\Enums\OtpPurpose::class],
            'security log event' => [\App\Enums\SecurityLogEvent::class],
            'two factor method' => [\App\Enums\TwoFactorMethod::class],
        ];
    }

    public function test_email_setup_guide_exists(): void
    {
        $this->assertFileExists(base_path('EMAIL_SETUP.md'));
    }

    public function test_api_routes_file_exists(): void
    {
        $this->assertFileExists(base_path('routes/api.php'));
    }

    public function test_billing_config_file_exists(): void
    {
        $this->assertFileExists(config_path('billing.php'));
    }
}
