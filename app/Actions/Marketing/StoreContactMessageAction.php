<?php

declare(strict_types=1);

namespace App\Actions\Marketing;

use App\Mail\ContactMessageSubmittedMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

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

        $supportEmail = (string) config('totalcashpro.support_email');

        if ($supportEmail !== '') {
            Mail::to($supportEmail)->send(new ContactMessageSubmittedMail($message));
        }

        return $message;
    }
}
