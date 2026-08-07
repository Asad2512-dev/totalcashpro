<x-layouts.business-admin title="Branches" active="branches">
    <x-admin.toolbar title="Branches" description="Manage locations, managers, banking and cash drawers.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-branch')">Add branch</x-admin.button>
    </x-admin.toolbar>

    @if ($branches->isEmpty())
        <x-admin.empty-state title="No branches yet" description="Add your first branch to get started." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="hidden px-4 py-3 md:table-cell">Manager</th>
                    <th class="hidden px-4 py-3 lg:table-cell">Contact</th>
                    <th class="hidden px-4 py-3 sm:table-cell">City</th>
                    <th class="px-4 py-3">Drawer</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($branches as $branch)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-semibold text-gray-900 dark:text-white" data-label="Name">
                            {{ $branch->name }}
                            <p class="mt-1 text-xs font-normal text-gray-500 md:hidden">{{ $branch->manager?->name ?? 'No manager' }}</p>
                        </td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Manager">{{ $branch->manager?->name ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 lg:table-cell" data-label="Contact">
                            {{ $branch->phone ?: '—' }}
                            @if ($branch->email)<br><span class="text-xs">{{ $branch->email }}</span>@endif
                        </td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="City">{{ $branch->city ?: '—' }}</td>
                        <td class="px-4 py-3.5" data-label="Drawer">£{{ number_format((float) ($branch->cashDrawer?->current_balance ?? 0), 2) }}</td>
                        <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                            <x-admin.table-action type="button" variant="neutral" x-data @click="$dispatch('open-modal', 'edit-branch-{{ $branch->id }}')">Edit</x-admin.table-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
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
