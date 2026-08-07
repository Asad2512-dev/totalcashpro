<x-layouts.staff title="Availability" active="availability">
    <x-admin.toolbar title="My Availability" description="Tell your manager when you can work each week." />

    <form method="POST" action="{{ route('staff.availability.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        @foreach ($days as $index => $label)
            @php
                $row = $existing->get($index);
            @endphp
            <x-admin.card>
                <input type="hidden" name="availability[{{ $index }}][day_of_week]" value="{{ $index }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 font-semibold text-primary-700 dark:bg-primary-900/30">{{ $label }}</span>
                        <label class="flex items-center gap-2 text-sm font-medium">
                            <input type="hidden" name="availability[{{ $index }}][is_available]" value="0">
                            <input type="checkbox" name="availability[{{ $index }}][is_available]" value="1" class="rounded border-gray-300 text-primary-600" @checked(old("availability.{$index}.is_available", $row?->is_available ?? true))>
                            Available
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:max-w-xs">
                        <x-admin.input type="time" name="availability[{{ $index }}][start_time]" label="From" :value="old('availability.'.$index.'.start_time', $row?->start_time ? substr((string) $row->start_time, 0, 5) : '')" />
                        <x-admin.input type="time" name="availability[{{ $index }}][end_time]" label="To" :value="old('availability.'.$index.'.end_time', $row?->end_time ? substr((string) $row->end_time, 0, 5) : '')" />
                    </div>
                </div>
            </x-admin.card>
        @endforeach
        <div class="flex justify-end">
            <x-admin.button type="submit">Save availability</x-admin.button>
        </div>
    </form>
</x-layouts.staff>
