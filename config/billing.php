<?php

declare(strict_types=1);

return [

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'gbp'),
    ],

    'trial' => [
        'days' => (int) env('BILLING_TRIAL_DAYS', 14),
        'reminder_days_before' => (int) env('BILLING_TRIAL_REMINDER_DAYS', 3),
    ],

];
