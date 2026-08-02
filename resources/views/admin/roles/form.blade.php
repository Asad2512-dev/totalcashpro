<x-layouts.admin :title="$role ? 'Edit Role' : 'Create Role'" active="roles">
    <x-admin.breadcrumb :items="['Roles', $role ? 'Edit' : 'Create']" />
    <x-admin.toolbar :title="$role ? 'Edit Role' : 'Create Role'" description="Assign permissions dynamically from the database.">
        <x-admin.button variant="secondary" size="sm" :href="route('super-admin.roles')">Back</x-admin.button>
    </x-admin.toolbar>

    <x-admin.card>
        <form method="POST" action="{{ $role ? route('super-admin.roles.update', $role) : route('super-admin.roles.store') }}" class="space-y-6">
            @csrf
            @if ($role) @method('PUT') @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input name="name" label="Name" :value="old('name', $role?->name)" />
                <x-admin.input name="slug" label="Slug" :value="old('slug', $role?->slug)" />
                <div class="sm:col-span-2">
                    <x-admin.input name="description" label="Description" :value="old('description', $role?->description)" />
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-display text-base font-bold">Permissions</h3>
                @foreach ($permissions as $group => $items)
                    <div>
                        <p class="mb-2 text-xs font-bold uppercase tracking-[0.12em] text-gray-400">{{ $group }}</p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($items as $permission)
                                <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm dark:border-gray-700">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $selected, true)) class="rounded border-gray-300 text-primary-600">
                                    <span>{{ $permission->name }} <span class="text-gray-400">({{ $permission->slug }})</span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <x-admin.button type="submit">Save role</x-admin.button>
        </form>
    </x-admin.card>
</x-layouts.admin>
