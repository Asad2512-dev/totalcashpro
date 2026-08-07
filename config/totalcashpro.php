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
        'title' => 'TotalCashPro — Restaurant Management, Attendance Kiosk & Finance Software UK',
        'description' => 'Cloud restaurant management software for UK businesses. Attendance kiosk with PIN clock-in, daily cash up, payroll, inventory, finance, CRM, multi-branch reports, 2FA security. Basic £19.99/mo · Professional £29.99/mo · 14-day trial.',
        'keywords' => 'restaurant management software, POS back office, attendance system, attendance kiosk, cash up software, payroll software, inventory software, UK restaurant management, hospitality software, retail management SaaS, TotalCashPro',
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
