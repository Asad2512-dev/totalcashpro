<?php

declare(strict_types=1);

namespace App\Services\Mail;

use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mailtrap\EmailHeader\CategoryHeader;
use Throwable;

final class MailSender
{
    /**
     * @param  string|array<int, string>  $to
     */
    public function send(
        string|array $to,
        string $subject,
        string $text,
        ?string $html = null,
        ?string $category = null,
    ): void {
        $recipients = is_array($to) ? $to : [$to];

        Mail::send([], [], function ($message) use ($recipients, $subject, $text, $html, $category): void {
            $message->to($recipients)
                ->subject($subject)
                ->text($text);

            if ($html !== null) {
                $message->html($html);
            }

            if ($category !== null) {
                $message->getHeaders()->add(new CategoryHeader($category));
            }
        });
    }

    /**
     * @param  string|array<int, string>  $to
     */
    public function sendMailable(MailableContract $mailable, string|array $to): void
    {
        Mail::to($to)->send($mailable);
    }

    public function sendRaw(string $body, callable $callback): void
    {
        Mail::raw($body, $callback);
    }

    public function trySend(callable $callback): bool
    {
        try {
            $callback($this);

            return true;
        } catch (Throwable $exception) {
            Log::error('Mail send failed.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
