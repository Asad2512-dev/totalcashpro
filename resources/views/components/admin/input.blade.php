@props([
    'label' => null,
    'type' => 'text',
    'name' => null,
])

<label class="admin-field">
    @if ($label)
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</span>
    @endif
    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('admin-input') }}
    >
</label>
