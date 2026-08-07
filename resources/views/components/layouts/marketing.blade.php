@props([
    'seo' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#16A34A">

    @php
        $seoTitle = $seo['title'] ?? config('totalcashpro.seo.title');
        $seoDescription = $seo['description'] ?? config('totalcashpro.seo.description');
        $canonical = $seo['canonical'] ?? url()->current();
        $ogImage = url(config('totalcashpro.seo.og_image'));
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ config('totalcashpro.seo.keywords') }}">
    <meta name="author" content="{{ brand_name() }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ brand_name() }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ config('totalcashpro.seo.twitter_handle') }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => brand_name(),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'url' => config('totalcashpro.domain'),
            'description' => config('totalcashpro.seo.description'),
            'offers' => [
                '@type' => 'Offer',
                'price' => '19.99',
                'priceCurrency' => 'GBP',
                'description' => 'Basic plan from £19.99/month with 14-day Professional trial',
            ],
            'featureList' => 'Attendance Kiosk, PIN Clock In, Cash Up, Payroll, Inventory, Finance, CRM, HR, Reports, Multi-Branch, 2FA',
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => brand_name(),
            'url' => config('totalcashpro.domain'),
            'logo' => brand_logo_url(),
            'description' => config('totalcashpro.seo.description'),
            'email' => config('totalcashpro.support_email'),
            'sameAs' => array_values(array_filter(config('totalcashpro.social'), fn ($url) => $url !== '#')),
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
    </script>

    <x-brand-favicon />
    <style>[x-cloak]{display:none!important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-xl focus:bg-navy-900 focus:px-4 focus:py-2 focus:text-white"
    >
        Skip to content
    </a>

    <x-navbar />

    <main id="main-content">
        {{ $slot }}
    </main>

    <x-footer />
</body>
</html>
