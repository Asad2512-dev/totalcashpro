@props([
    'branches' => [],
    'selectedBranchId' => null,
    'action' => '',
    'compact' => false,
])

<form method="POST" action="{{ $action }}" {{ $attributes->class($compact ? 'flex min-w-0 flex-1 items-center' : 'flex items-center gap-2') }}>
    @csrf
    <label for="{{ $compact ? 'branch-selector-mobile' : 'branch-selector' }}" class="sr-only">Branch</label>
    <select
        id="{{ $compact ? 'branch-selector-mobile' : 'branch-selector' }}"
        name="branch_id"
        onchange="this.form.submit()"
        @class([
            'admin-input min-h-[44px] w-full min-w-0 truncate py-2.5 text-sm font-medium shadow-sm sm:max-w-[14rem]',
            'rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900' => ! $compact,
        ])
    >
        <option value="all" @selected($selectedBranchId === null)>All branches</option>
        @foreach ($branches as $branch)
            <option value="{{ $branch->id }}" @selected($selectedBranchId === (int) $branch->id)>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
</form>
