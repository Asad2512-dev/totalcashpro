<x-layouts.business-admin title="Payroll" active="payroll">
    <x-admin.toolbar title="Payroll" description="Live wage records for the selected branch.">
        @foreach (['current' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'] as $key => $label)
            <x-admin.nav-pill :href="route('business-admin.payroll', ['period' => $key])" :active="$period === $key">{{ $label }}</x-admin.nav-pill>
        @endforeach
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-wage')">Add wage</x-admin.button>
    </x-admin.toolbar>

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wage records" description="Create wage entries from hours worked." />
    @else
        <x-admin.table
            :columns="['Staff', 'Hours', 'Amount', 'Status', 'Paid date', 'Notes', '']"
            :rows="$wages->map(function ($wage) {
                $status = $wage->status instanceof \BackedEnum ? $wage->status->value : (string) $wage->status;
                $pending = strcasecmp($status, 'Paid') !== 0;
                $action = $pending
                    ? '<form method=\"POST\" action=\"'.e(route('business-admin.payroll.paid', $wage)).'\" class=\"inline\">'.csrf_field().'<button type=\"submit\" class=\"admin-touch-target font-semibold text-primary-700\">Mark paid</button></form>'
                    : '—';
                return [
                    $wage->user?->name ?? '—',
                    number_format((float) $wage->hours_worked, 2).'h',
                    '£'.number_format((float) $wage->amount, 2),
                    $status,
                    $wage->paid_date?->format('d M Y') ?? '—',
                    $wage->notes ?: '—',
                    $action,
                ];
            })->all()"
            :raw-html="true"
        />
        <x-admin.pagination :paginator="$wages" />
    @endif

    <x-admin.modal name="add-wage" title="Add wage">
        <form method="POST" action="{{ route('business-admin.payroll.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Staff</label>
                <select name="user_id" required class="admin-input min-h-[44px] w-full">
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}{{ $member->hourly_rate ? ' (£'.number_format((float) $member->hourly_rate, 2).'/h)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-grid">
                <x-admin.input type="number" name="hours_worked" label="Hours worked" step="0.01" min="0" required />
                <x-admin.input type="number" name="hourly_rate" label="Hourly rate (£)" step="0.01" min="0" />
            </div>
            <x-admin.textarea name="notes" label="Notes" rows="2" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
