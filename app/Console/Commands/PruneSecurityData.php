<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OtpCode;
use Illuminate\Console\Command;

final class PruneSecurityData extends Command
{
    protected $signature = 'security:prune';

    protected $description = 'Remove expired OTP codes and old login history';

    public function handle(): int
    {
        $otpDeleted = OtpCode::query()
            ->where('expires_at', '<', now()->subDay())
            ->delete();

        $historyDeleted = \App\Models\LoginHistory::query()
            ->where('logged_in_at', '<', now()->subMonths(6))
            ->delete();

        $this->info("Pruned {$otpDeleted} OTP codes and {$historyDeleted} login history rows.");

        return self::SUCCESS;
    }
}
