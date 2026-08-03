@props([
    'title' => 'Sign in',
    'seo' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#16A34A">
    <title>{{ $seo['title'] ?? ($title.' — '.brand_name()) }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Sign in to TotalCashPro.' }}">
    <link rel="icon" href="{{ brand_favicon_url() }}" type="image/png" sizes="any">
    <link rel="apple-touch-icon" href="{{ brand_apple_touch_icon_url() }}">
    <link rel="shortcut icon" href="{{ brand_favicon_url() }}">
    <style>[x-cloak]{display:none!important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    {{ $slot }}
</body>
</html>
