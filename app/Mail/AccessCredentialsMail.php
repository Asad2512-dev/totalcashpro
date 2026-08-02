<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AccessCredentialsMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $plainPassword,
        public readonly Organization $organization,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TotalCashPro login details',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('emails.access-credentials', [
                'user' => $this->user,
                'password' => $this->plainPassword,
                'organization' => $this->organization,
                'loginUrl' => route('login'),
            ])->render(),
        );
    }
}
