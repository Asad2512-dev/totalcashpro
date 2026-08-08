<x-layouts.business-admin title="Riders" active="riders">
    <x-admin.toolbar title="Riders" description="Delivery riders for purchase order collections." />

    <x-admin.card class="mb-6">
        <h3 class="mb-4 font-display text-lg font-bold">Add rider</h3>
        <form method="POST" action="{{ route('business-admin.riders.store') }}" class="grid gap-3 md:grid-cols-3">
            @csrf
            <x-admin.input name="name" label="Name" required />
            <x-admin.input type="email" name="email" label="Email" required />
            <x-admin.input name="phone" label="Phone" />
            <x-admin.input name="vehicle" label="Vehicle" />
            <div class="flex items-end"><button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white">Create rider</button></div>
        </form>
    </x-admin.card>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($riders as $rider)
            <x-admin.card>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $rider->user?->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $rider->user?->email }}</p>
                        <p class="text-sm text-gray-500">{{ $rider->phone ?: 'No phone' }} · {{ $rider->vehicle ?: 'No vehicle' }}</p>
                    </div>
                    <span @class(['rounded-full px-2 py-1 text-xs font-semibold', 'bg-emerald-100 text-emerald-800' => $rider->is_active, 'bg-gray-100 text-gray-600' => ! $rider->is_active])>
                        {{ $rider->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </x-admin.card>
        @endforeach
    </div>
</x-layouts.business-admin>
