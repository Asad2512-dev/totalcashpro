@props([
    'links' => [],
    'deleteUrl' => null,
    'deleteLabel' => 'Delete',
])

<x-admin.table-actions class="justify-end">
    @foreach ($links as $link)
        @php
            $variant = match (strtolower($link['label'])) {
                'view', 'edit', 'manage', 'load' => 'neutral',
                'delete', 'suspend' => 'danger',
                'mark paid', 'mark received', 'mark read', 'archive' => 'success',
                default => 'primary',
            };
        @endphp
        <x-admin.table-action :href="$link['url']" :variant="$variant">{{ $link['label'] }}</x-admin.table-action>
    @endforeach
    @if ($deleteUrl)
        <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('Are you sure you want to delete this record?')">
            @csrf
            @method('DELETE')
            <x-admin.table-action type="submit" variant="danger">{{ $deleteLabel }}</x-admin.table-action>
        </form>
    @endif
</x-admin.table-actions>
