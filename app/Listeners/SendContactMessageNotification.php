<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ContactMessageSubmitted;
use App\Notifications\ContactMessageSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendContactMessageNotification implements ShouldQueue
{
    public function handle(ContactMessageSubmitted $event): void
    {
        $supportEmail = (string) config('totalcashpro.support_email');

        if ($supportEmail === '') {
            return;
        }

        \Illuminate\Support\Facades\Notification::route('mail', $supportEmail)
            ->notify(new ContactMessageSubmittedNotification($event->message));
    }
}
