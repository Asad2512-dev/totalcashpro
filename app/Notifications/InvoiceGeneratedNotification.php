<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Bill $bill,
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
            ->subject('New bill generated — '.brand_name())
            ->markdown('emails.invoice-generated', [
                'user' => $notifiable,
                'bill' => $this->bill,
            ]);
    }
}
