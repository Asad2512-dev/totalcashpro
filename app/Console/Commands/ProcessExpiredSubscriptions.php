<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use Illuminate\Console\Command;

final class ProcessExpiredSubscriptions extends Command
{
    protected $signature = 'billing:process-expired-subscriptions';

    protected $description = 'Mark expired subscriptions and notify business admins';

    public function handle(): int
    {
        Subscription::query()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->with('organization.users')
            ->each(function (Subscription $subscription): void {
                $subscription->update(['status' => SubscriptionStatus::Expired->value]);

                $admin = $subscription->organization?->users()
                    ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                    ->first();

                if ($admin !== null) {
                    $admin->notify(new SubscriptionExpiredNotification($subscription));
                }
            });

        $this->info('Expired subscriptions processed.');

        return self::SUCCESS;
    }
}
