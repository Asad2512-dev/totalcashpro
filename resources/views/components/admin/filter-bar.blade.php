<form method="GET" {{ $attributes->class('admin-filter-bar mb-4 rounded-2xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800') }}>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_repeat(3,minmax(0,9rem))_auto] lg:items-center">
        <div class="relative sm:col-span-2 lg:col-span-1">
            <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search…"
                class="admin-input min-h-[44px] pl-10"
            >
        </div>
        <select name="status" class="admin-input min-h-[44px]">
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
        <select name="sort" class="admin-input min-h-[44px]">
            <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>Newest</option>
            <option value="name" @selected(request('sort') === 'name')>Name</option>
            <option value="status" @selected(request('sort') === 'status')>Status</option>
        </select>
        <select name="direction" class="admin-input min-h-[44px]">
            <option value="desc" @selected(request('direction', 'desc') === 'desc')>Desc</option>
            <option value="asc" @selected(request('direction') === 'asc')>Asc</option>
        </select>
        <x-admin.button type="submit" variant="secondary" class="w-full sm:w-auto">Filters</x-admin.button>
    </div>
</form>
