<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ShiftSwapRejected;
use App\Notifications\ShiftSwapStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotifyShiftSwapRejected implements ShouldQueue
{
    public function handle(ShiftSwapRejected $event): void
    {
        $swap = $event->swapRequest->loadMissing(['requester', 'rotaShift']);

        $shiftDate = $swap->rotaShift?->shift_date?->format('d M Y') ?? 'your shift';

        $swap->requester?->notify(new ShiftSwapStatusNotification(
            approved: false,
            shiftDate: $shiftDate,
        ));
    }
}
