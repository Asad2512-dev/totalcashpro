<x-layouts.business-admin title="Branches" active="branches">
    <x-admin.toolbar title="Branches" description="Manage locations, managers, banking and cash drawers.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-branch')">Add branch</x-admin.button>
    </x-admin.toolbar>

    @if ($branches->isEmpty())
        <x-admin.empty-state title="No branches yet" description="Add your first branch to get started." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap -mx-4 sm:mx-0">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="hidden md:table-cell px-4 py-3">Manager</th>
                            <th class="hidden lg:table-cell px-4 py-3">Contact</th>
                            <th class="px-4 py-3">City</th>
                            <th class="hidden sm:table-cell px-4 py-3">Drawer</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($branches as $branch)
                            <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $branch->name }}</p>
                                    <p class="text-xs text-gray-500 sm:hidden">{{ $branch->manager?->name ?? 'No manager' }}</p>
                                </td>
                                <td class="hidden md:table-cell px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $branch->manager?->name ?? '—' }}</td>
                                <td class="hidden lg:table-cell px-4 py-3.5 text-gray-700 dark:text-gray-200">
                                    {{ $branch->phone ?: '—' }}<br>
                                    <span class="text-xs">{{ $branch->email ?: '' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $branch->city ?: '—' }}</td>
                                <td class="hidden sm:table-cell px-4 py-3.5 text-gray-700 dark:text-gray-200">
                                    £{{ number_format((float) ($branch->cashDrawer?->current_balance ?? 0), 2) }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <button type="button" class="font-semibold text-primary-700" x-data @click="$dispatch('open-modal', 'edit-branch-{{ $branch->id }}')">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <x-admin.modal name="add-branch" title="Add branch">
        <form method="POST" action="{{ route('business-admin.branches.store') }}" class="space-y-4">
            @csrf
            @include('business-admin.branches._fields')
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    @foreach ($branches as $branch)
        <x-admin.modal name="edit-branch-{{ $branch->id }}" title="Edit branch">
            <form method="POST" action="{{ route('business-admin.branches.update', $branch) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('business-admin.branches._fields', ['branch' => $branch])
                <div class="flex justify-end gap-2">
                    <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                    <x-admin.button type="submit">Update</x-admin.button>
                </div>
            </form>
        </x-admin.modal>
    @endforeach
</x-layouts.business-admin>
