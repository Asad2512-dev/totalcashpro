<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ContactMessageSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ContactMessage $message,
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
            ->subject('New contact message — '.$this->message->subject)
            ->markdown('emails.contact-message-submitted', [
                'message' => $this->message,
            ]);
    }
}
