@props([
    'label' => null,
    'name' => null,
    'checked' => false,
])

<label class="inline-flex cursor-pointer items-center gap-3" x-data="{ on: @js((bool) $checked) }">
    <button
        type="button"
        role="switch"
        :aria-checked="on.toString()"
        @click="on = !on"
        :class="on ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600'"
        class="relative h-6 w-11 shrink-0 rounded-full transition"
    >
        <span
            class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transition"
            :class="on ? 'translate-x-5' : 'translate-x-0'"
        ></span>
    </button>
    @if ($name)
        <input type="hidden" name="{{ $name }}" :value="on ? '1' : '0'">
    @endif
    @if ($label)
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
