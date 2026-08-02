@props([
    'tone' => 'success',
    'message' => null,
])

@php
    $toneClass = match ($tone) {
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'danger' => 'border-red-200 bg-red-50 text-red-800',
        'info' => 'border-sky-200 bg-sky-50 text-sky-800',
        default => 'border-primary-200 bg-primary-50 text-primary-800',
    };
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 4200)"
    x-transition
    {{ $attributes->class("admin-slide-up fixed right-4 bottom-4 z-[60] max-w-sm rounded-2xl border px-4 py-3 text-sm font-medium shadow-lift {$toneClass}") }}
    role="status"
>
    <div class="flex items-start gap-3">
        <span class="flex-1">{{ $message ?? $slot }}</span>
        <button type="button" class="opacity-70 hover:opacity-100" @click="show = false">
            <x-admin.icon name="x" class="h-4 w-4" />
        </button>
    </div>
</div>
