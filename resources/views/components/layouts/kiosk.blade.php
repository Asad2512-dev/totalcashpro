@props([
    'title' => 'Attendance Kiosk',
    'branchName' => null,
    'organizationName' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <x-theme-init />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#16A34A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('kiosk-manifest.webmanifest') }}">

    <title>{{ $title }} · {{ $organizationName ?? brand_name() }}</title>

    <x-brand-favicon />
    <style>[x-cloak]{display:none!important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="kiosk-shell min-h-full bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-white">
    {{ $slot }}

    @stack('scripts')
</body>
</html>
