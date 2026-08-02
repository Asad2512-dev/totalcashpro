@props([
    'label' => null,
    'name' => null,
    'value' => '1',
])

<label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-gray-700 dark:text-gray-200">
    <input
        type="checkbox"
        value="{{ $value }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600/30 dark:border-gray-600 dark:bg-gray-900') }}
    >
    @if ($label)
        <span>{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
