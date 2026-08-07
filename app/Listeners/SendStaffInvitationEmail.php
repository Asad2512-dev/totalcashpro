<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\StaffInvited;
use App\Notifications\StaffInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendStaffInvitationEmail implements ShouldQueue
{
    public function handle(StaffInvited $event): void
    {
        $event->staff->notify(new StaffInvitationNotification(
            $event->temporaryPassword,
            $event->invitedBy,
        ));
    }
}
