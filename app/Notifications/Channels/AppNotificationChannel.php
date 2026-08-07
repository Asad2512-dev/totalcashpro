<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Enums\NotificationCategory;
use App\Models\AppNotification;
use App\Services\Security\NotificationPreferenceService;
use Illuminate\Notifications\Notification;

final class AppNotificationChannel
{
    public function __construct(
        private readonly NotificationPreferenceService $preferences,
    ) {}

    /**
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toAppNotification')) {
            return;
        }

        $data = $notification->toAppNotification($notifiable);
        $category = $data['category'] ?? NotificationCategory::System;

        if (! $this->preferences->wantsDatabase($notifiable, $category)) {
            return;
        }

        AppNotification::query()->create([
            'user_id' => $notifiable->id,
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'type' => $data['type'] ?? 'system',
            'category' => $category,
            'priority' => $data['priority'] ?? 'normal',
            'data' => $data['data'] ?? null,
        ]);
    }
}
