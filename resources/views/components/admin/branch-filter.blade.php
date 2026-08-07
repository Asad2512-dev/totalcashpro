@props([
    'compact' => false,
])

@php
    $user = auth()->user();
    $branches = $user ? app(\App\Services\BusinessAdmin\BusinessAdminUiService::class)->branchesFor($user) : collect();
    $selectedBranchId = $user ? app(\App\Services\BusinessAdmin\BusinessAdminUiService::class)->selectedBranchId($user) : null;
@endphp

@if ($branches->count() > 1)
    <div
        {{ $attributes->class([
            'admin-branch-filter',
            'admin-branch-filter-compact' => $compact,
        ]) }}
    >
        @unless ($compact)
            <div class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <x-admin.icon name="building" class="h-4 w-4 shrink-0 text-primary-600" />
                <span class="font-medium">View data for</span>
            </div>
        @endunless

        <div class="admin-branch-tabs" role="tablist" aria-label="Branch filter">
            @foreach (collect([['id' => null, 'name' => 'All branches']])->merge($branches->map(fn ($branch) => ['id' => $branch->id, 'name' => $branch->name])) as $option)
                @php
                    $isActive = $option['id'] === null
                        ? $selectedBranchId === null
                        : $selectedBranchId === (int) $option['id'];
                @endphp
                <form method="POST" action="{{ route('business-admin.branch.select') }}" class="admin-branch-tab-form">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $option['id'] ?? 'all' }}">
                    <input type="hidden" name="silent" value="1">
                    <button
                        type="submit"
                        role="tab"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        @class([
                            'admin-branch-tab',
                            'admin-branch-tab-active' => $isActive,
                            'admin-branch-tab-inactive' => ! $isActive,
                        ])
                    >
                        {{ $option['name'] }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
@endif
