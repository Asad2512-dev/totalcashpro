@props(['items' => []])

<nav class="mb-4 flex flex-wrap items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
    <a href="{{ route('super-admin.dashboard') }}" class="hover:text-primary-700">Dashboard</a>
    @foreach ($items as $item)
        <span class="text-gray-300">/</span>
        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $item }}</span>
    @endforeach
</nav>
