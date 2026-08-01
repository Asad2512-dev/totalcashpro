<?php

declare(strict_types=1);

return [

    'name' => env('APP_NAME', 'TotalCashPro'),

    'tagline' => 'Complete Restaurant & Retail Operations Platform',

    'alt_tagline' => 'One payment. Unlimited business operations.',

    'domain' => env('APP_URL', 'https://totalcashpro.com'),

    'support_email' => env('SUPPORT_EMAIL', 'hello@totalcashpro.com'),

    /*
    |--------------------------------------------------------------------------
    | Licensing
    |--------------------------------------------------------------------------
    */

    'pricing' => [
        'amount' => '£29',
        'label' => 'Lifetime License',
        'badge' => 'One-Time Payment',
        'currency' => 'GBP',
        'model' => 'one_time',
    ],

    'seo' => [
        'title' => 'TotalCashPro — Restaurant & Retail Software for £29',
        'description' => 'TotalCashPro is professional restaurant and retail management software for £29 one-time. Cash up, attendance, payroll, inventory, suppliers, reports, and role management — no monthly fees.',
        'keywords' => 'restaurant software, retail management, cash up, payroll, inventory, attendance, one-time license, TotalCashPro',
        'og_image' => '/images/og-default.svg',
        'twitter_handle' => '@totalcashpro',
    ],

    'social' => [
        'twitter' => '#',
        'linkedin' => '#',
        'facebook' => '#',
        'instagram' => '#',
    ],

];
