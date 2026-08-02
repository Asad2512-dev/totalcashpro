<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrganizationRegistered;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

final class SendWelcomeEmail
{
    public function handle(OrganizationRegistered $event): void
    {
        Mail::to($event->result->owner->email)->send(
            new WelcomeMail($event->result->owner, $event->result->organization),
        );
    }
}
