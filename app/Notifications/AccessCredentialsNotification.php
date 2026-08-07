<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccessCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $password,
        private readonly Organization $organization,
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
            ->subject('Your '.brand_name().' login credentials')
            ->markdown('emails.access-credentials', [
                'user' => $notifiable,
                'organization' => $this->organization,
                'password' => $this->password,
                'loginUrl' => route('login'),
            ]);
    }
}
