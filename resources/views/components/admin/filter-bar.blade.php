@php
    $activeCount = (int) (request('search') !== null && request('search') !== '')
        + (int) (request('status') !== null && request('status') !== '')
        + (int) (request('sort', 'created_at') !== 'created_at')
        + (int) (request('direction', 'desc') !== 'desc');
@endphp

<x-admin.filter-sheet title="Search & filters" :active-count="$activeCount" class="mb-3 lg:mb-4">
    <form method="GET" id="admin-filter-form" {{ $attributes->merge(['class' => 'space-y-3']) }}>
        <div class="relative">
            <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search…"
                class="admin-input min-h-[44px] w-full pl-10"
            >
        </div>
        <select name="status" class="admin-input min-h-[44px] w-full">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="trial" @selected(request('status') === 'trial')>Trial</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            <option value="trialing" @selected(request('status') === 'trialing')>Trialing</option>
            <option value="past_due" @selected(request('status') === 'past_due')>Past Due</option>
            <option value="expired" @selected(request('status') === 'expired')>Expired</option>
            <option value="open" @selected(request('status') === 'open')>Open</option>
            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
            <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
        <select name="sort" class="admin-input min-h-[44px] w-full">
            <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>Newest</option>
            <option value="name" @selected(request('sort') === 'name')>Name</option>
            <option value="status" @selected(request('sort') === 'status')>Status</option>
        </select>
        <select name="direction" class="admin-input min-h-[44px] w-full">
            <option value="desc" @selected(request('direction', 'desc') === 'desc')>Desc</option>
            <option value="asc" @selected(request('direction') === 'asc')>Asc</option>
        </select>
        <x-admin.button type="submit" variant="secondary" class="hidden w-full min-h-[44px] lg:inline-flex">Apply filters</x-admin.button>
    </form>
    <x-slot:footer>
        <button type="submit" form="admin-filter-form" class="admin-touch-target inline-flex flex-1 items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white" @click="filterOpen = false">Apply</button>
        <a href="{{ url()->current() }}" class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">Clear</a>
    </x-slot:footer>
</x-admin.filter-sheet>
