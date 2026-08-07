<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Enums\TwoFactorMethod;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\Security\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class TwoFactorAuthenticationTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_user_can_enable_email_two_factor(): void
    {
        $user = $this->makeBusinessAdmin();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::TwoFactorSetup,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $codes = app(TwoFactorService::class)->enableWithEmail($user, '123456', request());

        $user->refresh();
        $this->assertTrue($user->two_factor_enabled);
        $this->assertSame(TwoFactorMethod::Email, $user->two_factor_method);
        $this->assertCount(8, $codes);
        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::TwoFactorEnabled->value,
        ]);
    }

    public function test_login_redirects_to_two_factor_challenge_when_enabled(): void
    {
        $user = $this->makeBusinessAdmin();
        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_method' => TwoFactorMethod::Email->value,
            'two_factor_confirmed_at' => now(),
        ])->save();

        Notification::fake();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.challenge'));

        $this->assertGuest();
    }

    public function test_two_factor_challenge_page_loads_with_pending_session(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->withSession(['login.id' => $user->id])
            ->get(route('two-factor.challenge'))
            ->assertOk()
            ->assertSee('Verify your sign-in', false);
    }

    public function test_two_factor_login_completes_with_valid_otp(): void
    {
        $user = $this->makeBusinessAdmin();
        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_method' => TwoFactorMethod::Email->value,
            'two_factor_confirmed_at' => now(),
        ])->save();

        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => OtpPurpose::TwoFactorLogin,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession(['login.id' => $user->id, 'login.remember' => false])
            ->post(route('two-factor.verify'), ['otp' => '123456'])
            ->assertRedirect(route('business-admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_recovery_code_can_complete_login(): void
    {
        $user = $this->makeBusinessAdmin();
        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_method' => TwoFactorMethod::Email->value,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $plain = 'ABCD-EFGH';
        \App\Models\TwoFactorRecoveryCode::query()->create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($plain),
        ]);

        $this->assertTrue(app(TwoFactorService::class)->useRecoveryCode($user, $plain, request()));
    }

    public function test_user_can_disable_two_factor(): void
    {
        $user = $this->makeBusinessAdmin();
        $user->forceFill([
            'two_factor_enabled' => true,
            'two_factor_method' => TwoFactorMethod::Email->value,
            'two_factor_confirmed_at' => now(),
        ])->save();

        app(TwoFactorService::class)->disable($user, request());

        $user->refresh();
        $this->assertFalse($user->two_factor_enabled);
        $this->assertDatabaseHas('security_logs', [
            'event' => SecurityLogEvent::TwoFactorDisabled->value,
        ]);
    }

    public function test_security_page_shows_two_factor_section(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->actingAsVerified($user)
            ->get(route('business-admin.security.index'))
            ->assertOk()
            ->assertSee('Two-factor authentication', false);
    }
}
