<?php

declare(strict_types=1);

namespace App\Actions\Marketing;

use App\Events\ContactMessageSubmitted;
use App\Models\ContactMessage;

final class StoreContactMessageAction
{
    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     phone?: string|null,
     *     subject: string,
     *     message: string
     * }  $data
     */
    public function execute(array $data): ContactMessage
    {
        $message = ContactMessage::query()->create($data);

        ContactMessageSubmitted::dispatch($message);

        return $message;
    }
}
