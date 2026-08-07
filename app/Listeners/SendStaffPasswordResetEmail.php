<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\StaffPasswordReset;
use App\Notifications\StaffPasswordResetNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendStaffPasswordResetEmail implements ShouldQueue
{
    public function handle(StaffPasswordReset $event): void
    {
        $event->staff->notify(new StaffPasswordResetNotification(
            $event->temporaryPassword,
            $event->resetBy,
        ));
    }
}
