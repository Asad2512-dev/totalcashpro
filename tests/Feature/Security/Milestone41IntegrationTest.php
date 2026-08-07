<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Events\StaffInvited;
use App\Models\User;
use App\Notifications\StaffInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Milestone41IntegrationTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_unverified_user_is_redirected_from_business_admin_dashboard(): void
    {
        $user = $this->makeBusinessAdmin();
        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAs($user)
            ->get(route('business-admin.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_logout_records_security_log_and_device_sign_out(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->actingAsVerified($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => \App\Enums\SecurityLogEvent::Logout->value,
        ]);

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $user->id,
            'event_type' => 'logout',
        ]);
    }

    public function test_staff_invitation_dispatches_event_and_notification(): void
    {
        Event::fake([StaffInvited::class]);
        Notification::fake();

        $admin = $this->makeBusinessAdmin();

        app(\App\Services\BusinessAdmin\StaffService::class)->create($admin, [
            'name' => 'New Staff',
            'email' => 'newstaff@test.com',
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('1234'),
        ]);

        Event::assertDispatched(StaffInvited::class);
    }

    public function test_notification_preferences_can_be_saved(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->actingAsVerified($user)
            ->post(route('business-admin.security.notifications.update'), [
                'preferences' => [
                    'staff' => ['email' => '1', 'database' => '0'],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'category' => 'staff',
            'email_enabled' => true,
            'database_enabled' => false,
        ]);
    }

    public function test_super_admin_email_queue_page_loads(): void
    {
        $this->seed([\Database\Seeders\RolePermissionSeeder::class, \Database\Seeders\SuperAdminSeeder::class]);
        $admin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('super-admin.email-queue'))
            ->assertOk()
            ->assertSee('Queue Monitor', false);
    }

    public function test_registration_redirects_to_email_verification(): void
    {
        $this->seed([\Database\Seeders\RolePermissionSeeder::class, \Database\Seeders\PlanSeeder::class]);

        $this->post(route('register.store'), [
            'business_name' => 'Verify Cafe',
            'owner_name' => 'Owner',
            'email' => 'owner@verifycafe.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'country' => 'GB',
            'business_type' => 'cafe',
            'terms' => '1',
        ])->assertRedirect(route('verification.notice'));
    }
}
