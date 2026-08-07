@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
])

@if ($type === 'textarea')
    <x-admin.textarea :name="$name" :label="$label" rows="3" class="w-full" {{ $attributes }} />
@elseif ($type === 'file')
    <label class="block space-y-1.5">
        @if ($label)
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</span>
        @endif
        <input type="file" @if ($name) name="{{ $name }}" @endif {{ $attributes->class('admin-input w-full') }} @if ($required) required @endif>
    </label>
@else
    <x-admin.input
        :type="$type"
        :name="$name"
        :label="$label"
        :value="$value"
        class="w-full"
        {{ $attributes }}
        @if ($required) required @endif
    />
@endif
