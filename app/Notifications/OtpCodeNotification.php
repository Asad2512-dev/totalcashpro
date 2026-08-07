<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\OtpPurpose;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OtpCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $code,
        private readonly OtpPurpose $purpose,
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
            ->subject('Your '.brand_name().' verification code')
            ->markdown('emails.otp-code', [
                'user' => $notifiable,
                'code' => $this->code,
                'purpose' => $this->purpose->label(),
                'expiryMinutes' => \App\Services\Security\OtpService::EXPIRY_MINUTES,
            ]);
    }
}
