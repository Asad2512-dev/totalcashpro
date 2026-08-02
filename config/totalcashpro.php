<?php

declare(strict_types=1);

return [

    'name' => env('APP_NAME', 'TotalCashPro'),

    'tagline' => 'Cloud restaurant & retail operations software.',

    'alt_tagline' => 'Manage cash, staff, inventory and reports in one secure dashboard.',

    'brand' => [
        'primary' => '#16A34A',
        'primary_dark' => '#15803D',
        'surface' => '#FFFFFF',
        'background' => '#F9FAFB',
    ],

    'domain' => env('APP_URL', 'https://totalcashpro.com'),

    'support_email' => env('SUPPORT_EMAIL', 'hello@totalcashpro.com'),

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    */

    'pricing' => [
        'model' => 'monthly_subscription',
        'currency' => 'GBP',
        'basic' => [
            'amount' => '£19.99',
            'period' => '/month',
            'label' => 'Basic Plan',
        ],
        'professional' => [
            'amount' => '£29.99',
            'period' => '/month',
            'label' => 'Professional Plan',
            'popular' => 'MOST POPULAR',
        ],
    ],

    'seo' => [
        'title' => 'TotalCashPro — Cloud Restaurant & Retail Software',
        'description' => 'TotalCashPro is cloud SaaS for restaurants and retail. Basic £19.99/month or Professional £29.99/month. Request access — accounts are reviewed and created by our team.',
        'keywords' => 'restaurant software, retail management, cash up, payroll, inventory, attendance, SaaS, monthly subscription, TotalCashPro',
        'og_image' => '/logo.png',
        'twitter_handle' => '@totalcashpro',
    ],

    'social' => [
        'twitter' => '#',
        'linkedin' => '#',
        'facebook' => '#',
        'instagram' => '#',
    ],

];
