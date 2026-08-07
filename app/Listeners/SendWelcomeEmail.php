<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrganizationRegistered;
use App\Mail\WelcomeMail;
use App\Services\Mail\MailSender;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendWelcomeEmail implements ShouldQueue
{
    public function __construct(private readonly MailSender $mail) {}

    public function handle(OrganizationRegistered $event): void
    {
        $this->mail->sendMailable(
            new WelcomeMail($event->result->owner, $event->result->organization),
            $event->result->owner->email,
        );
    }
}
