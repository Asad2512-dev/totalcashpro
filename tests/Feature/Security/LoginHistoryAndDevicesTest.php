<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\SecurityLogEvent;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class LoginHistoryAndDevicesTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_successful_login_records_history(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $user->id,
            'success' => true,
        ]);
    }

    public function test_failed_login_records_history(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('login_histories', [
            'email' => $user->email,
            'success' => false,
        ]);
    }

    public function test_successful_login_registers_device(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $user->id,
            'is_current' => true,
        ]);
    }

    public function test_security_page_lists_login_history(): void
    {
        $user = $this->makeBusinessAdmin();

        LoginHistory::query()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'success' => true,
            'logged_in_at' => now(),
        ]);

        $this->actingAsVerified($user)
            ->get(route('business-admin.security.index'))
            ->assertOk()
            ->assertSee('Recent login history', false);
    }

    public function test_user_can_trust_device(): void
    {
        $user = $this->makeBusinessAdmin();
        $device = UserDevice::query()->create([
            'user_id' => $user->id,
            'device_name' => 'Test Device',
            'is_trusted' => false,
            'last_active_at' => now(),
        ]);

        $this->actingAsVerified($user)
            ->post(route('business-admin.security.devices.trust', $device))
            ->assertRedirect();

        $this->assertTrue($device->fresh()->is_trusted);
    }

    public function test_user_cannot_manage_another_users_device(): void
    {
        $user = $this->makeBusinessAdmin('a@test.com');
        $other = $this->makeBusinessAdmin('b@test.com');

        $device = UserDevice::query()->create([
            'user_id' => $other->id,
            'device_name' => 'Other Device',
            'last_active_at' => now(),
        ]);

        $this->actingAsVerified($user)
            ->post(route('business-admin.security.devices.trust', $device))
            ->assertForbidden();
    }

    public function test_login_writes_security_log_on_success(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event' => SecurityLogEvent::LoginSuccess->value,
        ]);
    }

    public function test_login_writes_security_log_on_failure(): void
    {
        $user = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'bad',
        ]);

        $this->assertDatabaseHas('security_logs', [
            'event' => SecurityLogEvent::LoginFailure->value,
        ]);
    }
}
