<x-layouts.business-admin :title="$drawer->name" active="cash-drawers">
    <x-admin.toolbar :title="$drawer->name" :description="$drawer->branch?->name.' · '.$drawer->code">
        <x-admin.button href="{{ route('business-admin.cash-drawers') }}" variant="secondary" size="sm">← Tills</x-admin.button>
        <x-admin.button :href="route('business-admin.cash-up', ['cash_drawer_id' => $drawer->id])" size="sm">Cash up</x-admin.button>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif

    <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat label="Opening float" :value="'£'.number_format($drawer->defaultOpeningFloat(), 2)" />
        <x-admin.stat label="Current balance" :value="'£'.number_format((float) $drawer->current_balance, 2)" />
        <x-admin.stat label="Status" :value="$drawer->drawerStatus()->label()" />
        <x-admin.stat label="Last cash up" :value="$drawer->lastCashUp?->cashup_date?->format('d M Y H:i') ?? '—'" />
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @if ($drawer->drawerStatus() !== \App\Enums\CashDrawerStatus::Active)
            <form method="POST" action="{{ route('business-admin.cash-drawers.activate', $drawer) }}">@csrf<button type="submit" class="admin-btn text-sm">Activate</button></form>
        @else
            <form method="POST" action="{{ route('business-admin.cash-drawers.deactivate', $drawer) }}">@csrf<button type="submit" class="admin-btn text-sm">Deactivate</button></form>
        @endif
        @if ($drawer->drawerStatus() === \App\Enums\CashDrawerStatus::Locked)
            <form method="POST" action="{{ route('business-admin.cash-drawers.unlock', $drawer) }}">@csrf<button type="submit" class="admin-btn text-sm">Unlock</button></form>
        @else
            <form method="POST" action="{{ route('business-admin.cash-drawers.lock', $drawer) }}">@csrf<button type="submit" class="admin-btn text-sm">Lock</button></form>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin.card title="Recent cash ups">
            @forelse ($cashUps as $cashUp)
                <div class="flex items-center justify-between border-b border-gray-100 py-3 text-sm last:border-0 dark:border-gray-800">
                    <div>
                        <p class="font-semibold">{{ $cashUp->cashup_date?->format('d M Y') }} · {{ $cashUp->shift instanceof \BackedEnum ? $cashUp->shift->value : $cashUp->shift }}</p>
                        <p class="text-gray-500">{{ $cashUp->creator?->name }}</p>
                    </div>
                    <div class="text-right">
                        <p>£{{ number_format($cashUp->physicalCashTotal(), 2) }}</p>
                        <p @class(['text-xs', 'text-emerald-600' => abs($cashUp->varianceAmount()) < 0.01, 'text-amber-600' => abs($cashUp->varianceAmount()) >= 0.01])>
                            {{ abs($cashUp->varianceAmount()) < 0.01 ? 'Balanced' : '£'.number_format($cashUp->varianceAmount(), 2) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No cash ups in this period.</p>
            @endforelse
        </x-admin.card>

        <x-admin.card title="Cash movements">
            @forelse ($movements as $movement)
                <div class="flex items-center justify-between border-b border-gray-100 py-3 text-sm last:border-0 dark:border-gray-800">
                    <div>
                        <p class="font-semibold">{{ $movement->type instanceof \BackedEnum ? $movement->type->value : $movement->type }}</p>
                        <p class="text-gray-500">{{ $movement->description }}</p>
                    </div>
                    <p class="font-semibold">£{{ number_format((float) $movement->amount, 2) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No movements in this period.</p>
            @endforelse
        </x-admin.card>
    </div>

    @if ($transferTargets->isNotEmpty() && $drawer->drawerStatus()->isUsableForCashUp())
        <x-admin.card class="mt-6" title="Transfer cash">
            <form method="POST" action="{{ route('business-admin.cash-drawers.transfer', $drawer) }}" class="admin-field-grid max-w-xl">
                @csrf
                <label class="admin-field">
                    <span class="admin-label">To till</span>
                    <select name="to_drawer_id" class="admin-input" required>
                        @foreach ($transferTargets as $target)
                            <option value="{{ $target->id }}">{{ $target->name }} ({{ $target->code }})</option>
                        @endforeach
                    </select>
                </label>
                <x-admin.input type="number" step="0.01" min="0.01" name="amount" label="Amount (£)" required />
                <x-admin.input name="reason" label="Reason" required />
                <div class="flex items-end">
                    <button type="submit" class="admin-btn admin-btn-primary">Transfer</button>
                </div>
            </form>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
