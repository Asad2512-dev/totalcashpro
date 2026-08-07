@props([
    'branches' => [],
    'selectedBranchId' => null,
    'action' => '',
    'compact' => false,
])

<form method="POST" action="{{ $action }}" {{ $attributes->class($compact ? 'admin-branch-select-form admin-branch-select-form-compact' : 'admin-branch-select-form') }}>
    @csrf
    <label for="{{ $compact ? 'branch-selector-mobile' : 'branch-selector' }}" class="sr-only">Branch</label>
    <div class="admin-branch-select-wrap">
        <select
            id="{{ $compact ? 'branch-selector-mobile' : 'branch-selector' }}"
            name="branch_id"
            onchange="this.form.submit()"
            class="admin-branch-select"
        >
            <option value="all" @selected($selectedBranchId === null)>All branches</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected($selectedBranchId === (int) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        <span class="admin-branch-select-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </span>
    </div>
</form>
