<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeaveRequestApproved;
use App\Notifications\LeaveStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotifyLeaveApproved implements ShouldQueue
{
    public function handle(LeaveRequestApproved $event): void
    {
        $request = $event->leaveRequest->loadMissing('user');

        $request->user?->notify(new LeaveStatusNotification(
            status: 'approved',
            startDate: $request->start_date->format('d M Y'),
            endDate: $request->end_date->format('d M Y'),
        ));
    }
}
