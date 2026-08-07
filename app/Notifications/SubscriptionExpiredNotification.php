<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SubscriptionExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Subscription $subscription,
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
            ->subject('Your '.brand_name().' subscription has expired')
            ->markdown('emails.subscription-expired', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
                'renewUrl' => route('business-admin.subscription.choose-plan'),
            ]);
    }
}
