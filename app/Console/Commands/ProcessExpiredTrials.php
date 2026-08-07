<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Command;

final class ProcessExpiredTrials extends Command
{
    protected $signature = 'billing:process-expired-trials';

    protected $description = 'Mark expired trial subscriptions';

    public function handle(): int
    {
        Subscription::query()
            ->where('status', SubscriptionStatus::Trialing->value)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->update(['status' => SubscriptionStatus::Expired->value]);

        $this->info('Expired trials processed.');

        return self::SUCCESS;
    }
}
