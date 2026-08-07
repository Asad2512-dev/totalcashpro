<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Mail\MailSender;
use Illuminate\Support\ServiceProvider;

final class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MailSender::class);
    }
}
