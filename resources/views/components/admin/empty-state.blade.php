@props([
    'title' => 'Nothing here yet',
    'description' => 'Content will appear once this module is connected.',
])

<div {{ $attributes->class('admin-card flex flex-col items-center justify-center px-6 py-16 text-center') }}>
    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
        <x-admin.icon name="inbox" class="h-5 w-5" />
    </div>
    <h3 class="mt-4 font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-2 max-w-sm text-sm text-gray-500">{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
