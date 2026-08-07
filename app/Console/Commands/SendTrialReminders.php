<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\TrialEndingNotification;
use Illuminate\Console\Command;

final class SendTrialReminders extends Command
{
    protected $signature = 'billing:send-trial-reminders';

    protected $description = 'Send trial ending reminder emails';

    public function handle(): int
    {
        $days = config('billing.trial.reminder_days_before', 3);
        $targetDate = now()->addDays($days)->toDateString();

        Organization::query()
            ->whereHas('subscriptions', fn ($q) => $q->where('status', SubscriptionStatus::Trialing->value)->whereDate('trial_ends_at', $targetDate))
            ->with('users')
            ->each(function (Organization $org): void {
                $admin = $org->users()->whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();
                if ($admin !== null) {
                    $admin->notify(new TrialEndingNotification($org));
                }
            });

        $this->info('Trial reminder emails queued.');

        return self::SUCCESS;
    }
}
