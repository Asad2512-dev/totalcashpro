<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ContactMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ContactMessageSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ContactMessage $message,
    ) {}
}
