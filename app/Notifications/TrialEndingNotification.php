<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TrialEndingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
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
            ->subject('Your '.brand_name().' trial is ending soon')
            ->markdown('emails.trial-ending', [
                'user' => $notifiable,
                'organization' => $this->organization,
                'daysRemaining' => config('billing.trial.reminder_days_before', 3),
                'choosePlanUrl' => route('business-admin.subscription.choose-plan'),
            ]);
    }
}
