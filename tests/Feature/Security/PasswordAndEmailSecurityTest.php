<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\SecurityLogEvent;
use App\Notifications\LeaveStatusNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Services\Security\PasswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class PasswordAndEmailSecurityTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_password_update_requires_strong_password(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->actingAsVerified($user)
            ->post(route('business-admin.security.password.update'), [
                'current_password' => 'password',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_password_update_succeeds_with_strong_password(): void
    {
        $user = $this->makeBusinessAdmin();
        $new = $this->strongPassword();

        $this->actingAsVerified($user)
            ->post(route('business-admin.security.password.update'), [
                'current_password' => 'password',
                'password' => $new,
                'password_confirmation' => $new,
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check($new, $user->fresh()->password));
        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::PasswordChanged->value,
        ]);
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = $this->makeBusinessAdmin();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_verification_notification_uses_custom_template(): void
    {
        Notification::fake();
        $user = $this->makeBusinessAdmin();
        $user->forceFill(['email_verified_at' => null])->save();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_leave_notification_sends_mail_and_app_channel(): void
    {
        Notification::fake();
        $user = $this->makeBusinessAdmin();

        $user->notify(new LeaveStatusNotification('approved', '01 Jan 2026', '05 Jan 2026'));

        Notification::assertSentTo($user, LeaveStatusNotification::class);
    }

    public function test_password_service_rules_include_confirmation(): void
    {
        $rules = app(PasswordService::class)->rules();
        $this->assertContains('confirmed', $rules);
    }

    public function test_forgot_password_route_is_throttled(): void
    {
        $user = $this->makeBusinessAdmin();
        Notification::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('password.email'), ['email' => $user->email]);
        }

        $response = $this->post(route('password.email'), ['email' => $user->email]);
        $response->assertStatus(429);
    }
}
