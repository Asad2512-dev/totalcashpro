@props([
    'title',
    'description' => null,
    'active',
    'fields' => [],
    'actions' => null,
    'editRoute' => null,
    'backRoute',
    'credentials' => null,
])

<x-layouts.admin :title="$title" :active="$active">
    <x-admin.breadcrumb :items="[ucfirst(str_replace('-', ' ', $active)), $title]" />

    <x-admin.toolbar :title="$title" :description="$description">
        <x-admin.button variant="secondary" size="sm" :href="$backRoute">Back</x-admin.button>
        @if ($editRoute)
            <x-admin.button size="sm" :href="$editRoute">Edit</x-admin.button>
        @endif
        {{ $actions ?? '' }}
    </x-admin.toolbar>

    @if (! empty($credentials))
        <div class="mb-5 rounded-2xl border border-primary-200 bg-primary-50 px-5 py-4 text-sm text-primary-900 dark:border-primary-800 dark:bg-primary-900/20 dark:text-primary-100">
            <p class="font-semibold">Owner login credentials (share once, then ask them to change password)</p>
            <dl class="mt-3 grid gap-2 sm:grid-cols-3">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Owner</dt>
                    <dd class="font-medium">{{ $credentials['name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Email / login</dt>
                    <dd class="font-medium">{{ $credentials['email'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Temporary password</dt>
                    <dd class="font-mono font-semibold">{{ $credentials['password'] }}</dd>
                </div>
            </dl>
            <p class="mt-3 text-xs text-primary-800/80 dark:text-primary-200/80">An email was also sent to the owner. With local mail (log driver), copy these details manually if needed.</p>
        </div>
    @endif

    <x-admin.card>
        <dl class="grid gap-4 sm:grid-cols-2">
            @foreach ($fields as $field)
                <div @class(['sm:col-span-2' => $field['full'] ?? false])>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">{{ $field['label'] }}</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $field['value'] }}</dd>
                </div>
            @endforeach
        </dl>
    </x-admin.card>

    {{ $slot }}
</x-layouts.admin>
