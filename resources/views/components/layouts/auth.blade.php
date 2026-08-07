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
    <x-brand-favicon />
    <style>[x-cloak]{display:none!important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data
    class="min-h-screen bg-gray-50 text-gray-900 antialiased"
    @submit.window="if ($event.target.matches('form:not([data-no-auth-spinner])')) $store.authUi.start()"
>
    {{ $slot }}
    <x-auth.spinner />
</body>
</html>
