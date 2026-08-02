@props([
    'label' => null,
    'name' => null,
    'rows' => 4,
])

<label class="block space-y-1.5">
    @if ($label)
        <span class="admin-label">{{ $label }}</span>
    @endif
    <textarea
        rows="{{ $rows }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('admin-input min-h-[7rem] resize-y') }}
    >{{ $slot }}</textarea>
</label>
