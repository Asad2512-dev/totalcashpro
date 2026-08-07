<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class StaffPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $temporaryPassword,
        private readonly User $resetBy,
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
            ->subject('Your '.brand_name().' password was reset')
            ->markdown('emails.staff-password-reset', [
                'user' => $notifiable,
                'resetBy' => $this->resetBy,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => route('login'),
            ]);
    }
}
