<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OwnerCredentialsSent;
use App\Notifications\AccessCredentialsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendOwnerCredentialsEmail implements ShouldQueue
{
    public function handle(OwnerCredentialsSent $event): void
    {
        $event->owner->notify(new AccessCredentialsNotification(
            $event->password,
            $event->organization,
        ));
    }
}
