<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Notifications\Channels\AppNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ShiftSwapStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly bool $approved,
        private readonly string $shiftDate,
        private readonly ?string $partnerName = null,
    ) {}

    /**
     * @return array<int, string|class-string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', AppNotificationChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->approved ? 'Shift swap approved' : 'Shift swap declined')
            ->markdown('emails.shift-swap-status', [
                'user' => $notifiable,
                'approved' => $this->approved,
                'shiftDate' => $this->shiftDate,
                'partnerName' => $this->partnerName,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toAppNotification(object $notifiable): array
    {
        return [
            'title' => $this->approved ? 'Shift swap approved' : 'Shift swap declined',
            'body' => 'Your shift swap for '.$this->shiftDate.' was '.($this->approved ? 'approved' : 'declined').'.',
            'type' => 'shift_swap',
            'category' => NotificationCategory::Staff,
            'priority' => 'normal',
        ];
    }
}
