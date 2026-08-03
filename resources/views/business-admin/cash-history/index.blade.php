<x-layouts.business-admin title="Cash History" active="cash-history">
    <x-admin.toolbar title="Cash history" description="Live totals from saved cash ups for the selected branch filter.">
        @foreach (['daily', 'weekly', 'monthly'] as $p)
            <x-admin.nav-pill
                :href="route('business-admin.cash-history', ['period' => $p, 'date' => $date])"
                :active="$period === $p"
            >{{ ucfirst($p) }}</x-admin.nav-pill>
        @endforeach
        <form method="GET" class="inline-flex items-center gap-2">
            <input type="hidden" name="period" value="{{ $period }}">
            <x-admin.input type="date" name="date" :value="$date" onchange="this.form.submit()" />
        </form>
    </x-admin.toolbar>

    <div class="mb-6">
        <x-admin.stat
            label="Period total (net)"
            :value="'£'.number_format((float) $total, 2)"
            :change="$from->format('d M').' – '.$to->format('d M Y')"
            tone="success"
        />
    </div>

    @if ($rows->isEmpty())
        <x-admin.empty-state title="No cash ups in this period" description="Saved Morning/Evening cash ups will appear here in real time." />
    @else
        <x-admin.table
            :columns="['Date', 'Shift', 'Branch', 'Coins', 'Notes', 'Cards', 'Online', 'Expenses', 'Deductions', 'Net']"
            :rows="$rows->map(fn ($row) => [
                $row->cashup_date?->format('d M Y'),
                $row->shift instanceof \BackedEnum ? $row->shift->value : (string) $row->shift,
                $row->branch?->name ?? '—',
                '£'.number_format((float) $row->coins_total, 2),
                '£'.number_format((float) $row->notes_total, 2),
                '£'.number_format((float) $row->cards_total, 2),
                '£'.number_format((float) $row->online_orders_total, 2),
                '£'.number_format((float) $row->expenses_total, 2),
                '£'.number_format((float) $row->platform_deductions_total, 2),
                '£'.number_format($row->netTotal(), 2),
            ])->all()"
        />
    @endif
</x-layouts.business-admin>
