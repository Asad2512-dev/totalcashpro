<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class StaffInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $temporaryPassword,
        private readonly User $invitedBy,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been invited to '.brand_name())
            ->markdown('emails.staff-invitation', [
                'user' => $notifiable,
                'invitedBy' => $this->invitedBy,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => route('login'),
            ]);
    }
}
