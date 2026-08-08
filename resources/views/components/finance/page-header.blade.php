@props([
    'title',
    'description' => null,
])

<x-admin.mobile-page-header class="lg:hidden" :title="$title" :description="$description">
    @isset($actions)
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endisset
</x-admin.mobile-page-header>

<x-admin.toolbar :title="$title" :description="$description" class="hidden lg:flex">
    {{ $slot }}
</x-admin.toolbar>
