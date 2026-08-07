<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class SchedulerCommandTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_trial_reminder_command_runs(): void
    {
        $this->seedRolesAndPlans();
        $admin = $this->makeBusinessAdmin();

        Subscription::query()->where('organization_id', $admin->organization_id)->update([
            'status' => SubscriptionStatus::Trialing->value,
            'trial_ends_at' => now()->addDays(config('billing.trial.reminder_days_before', 3)),
        ]);

        $this->artisan('billing:send-trial-reminders')->assertSuccessful();
    }

    public function test_expired_subscription_command_runs(): void
    {
        $this->seedRolesAndPlans();
        $admin = $this->makeBusinessAdmin();

        Subscription::query()->where('organization_id', $admin->organization_id)->update([
            'status' => SubscriptionStatus::Active->value,
            'ends_at' => now()->subDay(),
        ]);

        $this->artisan('billing:process-expired-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'organization_id' => $admin->organization_id,
            'status' => SubscriptionStatus::Expired->value,
        ]);
    }

    public function test_recurring_bills_command_is_scheduled(): void
    {
        $events = Artisan::all();
        $this->assertArrayHasKey('finance:generate-recurring-bills', $events);
    }
}
