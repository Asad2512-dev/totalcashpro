@props([
    'label' => null,
    'name' => null,
])

<label class="admin-field">
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
