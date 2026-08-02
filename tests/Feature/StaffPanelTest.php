<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Enums\RoleSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StaffPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_login_and_open_dashboard(): void
    {
        $this->seed();

        $staff = User::query()->where('email', 'jamie@harbourkitchen.test')->first()
            ?? User::query()->where('email', 'staff.harbour-kitchen-group@totalcashpro.test')->first();

        $this->assertNotNull($staff);

        $this->post(route('login.attempt'), [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertRedirect(route('staff.dashboard'));

        $this->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Staff Dashboard');
    }

    public function test_staff_cannot_open_business_admin(): void
    {
        $this->seed();

        $staff = User::query()->where('email', 'staff.harbour-kitchen-group@totalcashpro.test')->firstOrFail();

        $this->actingAs($staff)
            ->get(route('business-admin.dashboard'))
            ->assertForbidden();
    }

    public function test_staff_clock_and_attendance_pages_load(): void
    {
        $this->seed();

        $staff = User::query()->where('email', 'staff.harbour-kitchen-group@totalcashpro.test')->firstOrFail();

        $this->actingAs($staff)
            ->get(route('staff.clock'))
            ->assertOk()
            ->assertSee('Clock In');

        $this->actingAs($staff)
            ->get(route('staff.attendance'))
            ->assertOk()
            ->assertSee('My Attendance');
    }

    public function test_admin_still_lands_on_business_dashboard(): void
    {
        $this->seed();

        $this->post(route('login.attempt'), [
            'email' => 'ava@harbourkitchen.test',
            'password' => 'password',
        ])->assertRedirect(route('business-admin.dashboard'));
    }
}
