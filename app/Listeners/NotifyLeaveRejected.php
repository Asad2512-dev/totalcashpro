<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeaveRequestRejected;
use App\Notifications\LeaveStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotifyLeaveRejected implements ShouldQueue
{
    public function handle(LeaveRequestRejected $event): void
    {
        $request = $event->leaveRequest->loadMissing('user');

        $request->user?->notify(new LeaveStatusNotification(
            status: 'rejected',
            startDate: $request->start_date->format('d M Y'),
            endDate: $request->end_date->format('d M Y'),
            adminNotes: $event->adminNotes,
        ));
    }
}
