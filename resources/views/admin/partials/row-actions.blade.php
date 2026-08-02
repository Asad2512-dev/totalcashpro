@props([
    'links' => [],
    'deleteUrl' => null,
    'deleteLabel' => 'Delete',
])

<div class="flex flex-wrap items-center justify-end gap-2">
    @foreach ($links as $link)
        <a href="{{ $link['url'] }}" class="font-semibold text-primary-700 hover:text-primary-800">{{ $link['label'] }}</a>
    @endforeach
    @if ($deleteUrl)
        <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('Are you sure you want to delete this record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="font-semibold text-red-600 hover:text-red-700">{{ $deleteLabel }}</button>
        </form>
    @endif
</div>
