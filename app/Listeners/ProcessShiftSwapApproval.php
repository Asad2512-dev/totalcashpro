<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ShiftSwapApproved;
use App\Models\RotaShift;
use App\Notifications\ShiftSwapStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

final class ProcessShiftSwapApproval implements ShouldQueue
{
    public function handle(ShiftSwapApproved $event): void
    {
        $swap = $event->swapRequest->loadMissing(['requester', 'targetUser', 'rotaShift']);

        DB::transaction(function () use ($swap, $event): void {
            $shift = RotaShift::query()->lockForUpdate()->findOrFail($swap->rota_shift_id);

            if ($swap->target_user_id !== null) {
                $shift->update(['user_id' => $swap->target_user_id]);
            }

            $swap->update([
                'reviewed_by' => $event->reviewer->id,
                'reviewed_at' => now(),
                'status' => \App\Enums\RequestStatus::Approved,
            ]);
        });

        $shiftDate = $swap->rotaShift?->shift_date?->format('d M Y') ?? 'your shift';

        $swap->requester?->notify(new ShiftSwapStatusNotification(
            approved: true,
            shiftDate: $shiftDate,
            partnerName: $swap->targetUser?->name,
        ));

        if ($swap->target_user_id && $swap->targetUser) {
            $swap->targetUser->notify(new ShiftSwapStatusNotification(
                approved: true,
                shiftDate: $shiftDate,
                partnerName: $swap->requester?->name,
            ));
        }
    }
}
