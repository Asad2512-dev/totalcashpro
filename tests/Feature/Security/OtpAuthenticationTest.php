<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeNotification;
use App\Services\Security\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class OtpAuthenticationTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_otp_is_hashed_in_database(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();

        app(OtpService::class)->generateAndSend($user, OtpPurpose::EmailVerification);

        $otp = OtpCode::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($otp);
        $this->assertNotSame('000000', $otp->code_hash);
        $this->assertTrue(strlen($otp->code_hash) > 20);
    }

    public function test_otp_notification_is_sent(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();

        app(OtpService::class)->generateAndSend($user, OtpPurpose::SensitiveAction);

        Notification::assertSentTo($user, OtpCodeNotification::class);
    }

    public function test_valid_otp_verifies_successfully(): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::EmailVerification,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->assertTrue(app(OtpService::class)->verify($user, OtpPurpose::EmailVerification, '123456'));
    }

    public function test_expired_otp_fails_verification(): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::EmailVerification,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->assertFalse(app(OtpService::class)->verify($user, OtpPurpose::EmailVerification, '123456'));
    }

    public function test_used_otp_cannot_be_reused(): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::EmailVerification,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'used_at' => now(),
        ]);

        $this->assertFalse(app(OtpService::class)->verify($user, OtpPurpose::EmailVerification, '123456'));
    }

    public function test_new_otp_invalidates_previous_codes(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::TwoFactorSetup,
            'code_hash' => Hash::make('111111'),
            'expires_at' => now()->addMinutes(10),
        ]);

        app(OtpService::class)->generateAndSend($user, OtpPurpose::TwoFactorSetup);

        $this->assertSame(1, OtpCode::query()->where('user_id', $user->id)->whereNull('used_at')->count());
    }

    public function test_otp_request_creates_security_log(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();

        app(OtpService::class)->generateAndSend($user, OtpPurpose::TwoFactorLogin);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::OtpRequested->value,
        ]);
    }

    public function test_otp_verification_creates_security_log(): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::SensitiveAction,
            'code_hash' => Hash::make('654321'),
            'expires_at' => now()->addMinutes(10),
        ]);

        app(OtpService::class)->verify($user, OtpPurpose::SensitiveAction, '654321');

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::OtpVerified->value,
        ]);
    }

    public function test_otp_rate_limit_blocks_excessive_requests(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();
        $service = app(OtpService::class);

        for ($i = 0; $i < 5; $i++) {
            $service->generateAndSend($user, OtpPurpose::EmailVerification);
        }

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $service->generateAndSend($user, OtpPurpose::EmailVerification);
    }

    #[DataProvider('invalidOtpProvider')]
    public function test_invalid_otp_codes_fail(string $code): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::EmailVerification,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->assertFalse(app(OtpService::class)->verify($user, OtpPurpose::EmailVerification, $code));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidOtpProvider(): array
    {
        return [
            'wrong code' => ['999999'],
            'empty' => [''],
            'partial' => ['12345'],
        ];
    }
}
