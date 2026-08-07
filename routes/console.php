<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:send-test {email?}', function () {
    $to = (string) ($this->argument('email') ?: config('mail.from.address'));

    if ($to === '') {
        $this->error('Provide a recipient email or set MAIL_FROM_ADDRESS in .env');

        return self::FAILURE;
    }

    $mailer = (string) config('mail.default');

    if ($mailer === 'smtp') {
        foreach (['MAIL_HOST' => config('mail.mailers.smtp.host'), 'MAIL_USERNAME' => config('mail.mailers.smtp.username')] as $label => $value) {
            if (blank($value)) {
                $this->error("{$label} is not set in .env");

                return self::FAILURE;
            }
        }
    }

    try {
        Mail::raw(
            'This is a test email from '.config('app.name').'. If you received this, your mail configuration is working.',
            function ($message) use ($to): void {
                $message->to($to)
                    ->subject(config('app.name').' — SMTP test');
            },
        );
    } catch (Throwable $exception) {
        $this->error('Mail send failed: '.$exception->getMessage());
        $this->line('Check storage/logs/laravel.log for details.');

        return self::FAILURE;
    }

    $this->info("Test email sent to {$to} via {$mailer} mailer.");

    return self::SUCCESS;
})->purpose('Send a test email using the configured MAIL_MAILER from .env');
