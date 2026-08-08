<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\NotificationCategory;
use App\Models\AppNotification;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\User;

final class RotaNotificationService implements ServiceInterface
{
    public function notifyPublished(RotaVersion $version, ?RotaVersion $previous): void
    {
        $staffIds = $version->shifts()->pluck('user_id')->unique();
        $weekLabel = $version->weekLabel();
        $changedIds = collect();

        if ($previous !== null) {
            $before = $previous->shifts()->get()->keyBy(fn (RotaShift $s) => $s->user_id.'|'.$s->shift_date?->toDateString().'|'.$s->shift_type);
            foreach ($version->shifts()->get() as $shift) {
                $key = $shift->user_id.'|'.$shift->shift_date?->toDateString().'|'.$shift->shift_type;
                $prev = $before->get($key);
                $label = $shift->start_time?->format('H:i').'–'.$shift->end_time?->format('H:i');
                $prevLabel = $prev ? $prev->start_time?->format('H:i').'–'.$prev->end_time?->format('H:i') : null;
                if ($prevLabel !== $label) {
                    $changedIds->push($shift->user_id);
                }
            }
        }

        foreach ($staffIds as $staffId) {
            $updated = $changedIds->contains($staffId);
            AppNotification::query()->create([
                'user_id' => $staffId,
                'title' => $updated ? 'Your rota has been updated' : 'New rota published',
                'body' => $updated
                    ? "Your rota for {$weekLabel} has been updated."
                    : "Your rota for {$weekLabel} has been published.",
                'type' => 'rota_published',
                'category' => NotificationCategory::Staff->value,
                'priority' => 'normal',
                'data' => [
                    'rota_version_id' => $version->id,
                    'week_start' => $version->week_start?->toDateString(),
                    'branch_id' => $version->branch_id,
                ],
            ]);
        }
    }
}
