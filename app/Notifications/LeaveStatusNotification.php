<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Notifications\Channels\AppNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $status,
        private readonly string $startDate,
        private readonly string $endDate,
        private readonly ?string $adminNotes = null,
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
        $approved = $this->status === 'approved';

        return (new MailMessage)
            ->subject($approved ? 'Leave request approved' : 'Leave request declined')
            ->markdown('emails.leave-status', [
                'user' => $notifiable,
                'approved' => $approved,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'adminNotes' => $this->adminNotes,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toAppNotification(object $notifiable): array
    {
        $approved = $this->status === 'approved';

        return [
            'title' => $approved ? 'Leave approved' : 'Leave declined',
            'body' => "Your leave from {$this->startDate} to {$this->endDate} was ".($approved ? 'approved' : 'declined').'.',
            'type' => 'leave',
            'category' => NotificationCategory::Staff,
            'priority' => 'normal',
        ];
    }
}
