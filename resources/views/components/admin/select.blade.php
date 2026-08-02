@props([
    'label' => null,
    'name' => null,
])

<label class="block space-y-1.5">
    @if ($label)
        <span class="admin-label">{{ $label }}</span>
    @endif
    <select
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('admin-input') }}
    >
        {{ $slot }}
    </select>
</label>
