<a
    href="{{ $href }}"
    {{ $attributes->class($classes()) }}
    @if ($external) target="_blank" rel="noopener noreferrer" @endif
>
    {{ $slot }}
</a>
