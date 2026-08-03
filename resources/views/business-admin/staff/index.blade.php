<x-layouts.business-admin title="Staff" active="staff">
    <x-admin.toolbar title="Staff" description="Manage team members, PINs, branches and rates.">
        <x-admin.button size="sm" :href="route('business-admin.staff.create')">Add staff</x-admin.button>
    </x-admin.toolbar>

    <form method="GET" class="mb-4 max-w-full sm:max-w-sm">
        <x-admin.input name="q" label="Search" :value="request('q')" placeholder="Name, email, PIN" />
    </form>

    @if ($staff->isEmpty())
        <x-admin.empty-state title="No staff yet" description="Add your first team member to start clock-in and payroll.">
            <x-admin.button :href="route('business-admin.staff.create')">Add staff</x-admin.button>
        </x-admin.empty-state>
    @else
        <x-admin.table
            :columns="['Name', 'Branch', 'PIN', 'Rate', 'Status', '']"
            :raw-html="true"
            :rows="$staff->map(fn ($member) => [
                '<div><p class=\"font-medium\">'.e($member->name).'</p><p class=\"text-xs text-gray-500\">'.e($member->email).'</p></div>',
                $member->branch?->name ?? '—',
                e($member->pin_code ?? '—'),
                '£'.number_format((float) ($member->hourly_rate ?? 0), 2),
                $member->status,
                '<div class=\"flex flex-wrap gap-2\"><a href=\"'.e(route('business-admin.staff.edit', $member)).'\" class=\"font-semibold text-primary-600\">Edit</a><form method=\"POST\" action=\"'.e(route('business-admin.staff.suspend', $member)).'\" class=\"inline\">'.csrf_field().'<button class=\"font-semibold text-amber-600\">Suspend</button></form></div>',
            ])->all()"
        />
        <x-admin.pagination :paginator="$staff" />
    @endif
</x-layouts.business-admin>
