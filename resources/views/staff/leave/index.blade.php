<x-layouts.staff title="Leave Requests" active="leave">
    <x-admin.toolbar title="Leave & Holidays" description="Request holiday, sick leave or other time off." />

    <x-admin.card class="mb-6">
        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">New request</h3>
        <form method="POST" action="{{ route('staff.leave.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                <select name="type" class="admin-input w-full" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input type="date" name="start_date" label="Start date" required />
            <x-admin.input type="date" name="end_date" label="End date" required />
            <div class="sm:col-span-2">
                <x-admin.textarea name="reason" label="Reason (optional)" rows="3" />
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <x-admin.button type="submit">Submit request</x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card :padding="false">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Your requests</h3>
        </div>
        @if ($requests->isEmpty())
            <p class="p-4 text-sm text-gray-500">No leave requests yet.</p>
        @else
            <div class="admin-mobile-cards p-4 sm:hidden">
                @foreach ($requests as $req)
                    <article class="admin-mobile-card">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $req->type->label() }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M Y') }}</p>
                        <p class="mt-2 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold dark:bg-gray-800">{{ ucfirst($req->status->value) }}</p>
                    </article>
                @endforeach
            </div>
            <div class="hidden sm:block">
                <x-admin.table
                    :columns="['Type', 'Dates', 'Status', 'Submitted']"
                    :rows="$requests->map(fn ($r) => [
                        $r->type->label(),
                        $r->start_date->format('d M').' – '.$r->end_date->format('d M Y'),
                        ucfirst($r->status->value),
                        $r->created_at?->format('d M Y'),
                    ])->all()"
                />
            </div>
        @endif
    </x-admin.card>
</x-layouts.staff>
