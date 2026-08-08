@props([
    'items' => [],
    'secondary' => [],
])

@php
    $allItems = array_merge($items, $secondary);
@endphp

<div {{ $attributes->class('admin-stat-grid--compact lg:hidden') }}>
    @foreach ($allItems as $item)
        <x-admin.stat
            compact
            :label="$item['label']"
            :value="$item['value']"
            :change="$item['change'] ?? null"
            :tone="$item['tone'] ?? 'neutral'"
        />
    @endforeach
</div>
