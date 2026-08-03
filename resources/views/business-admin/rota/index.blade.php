@php
    $prevWeek = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $nextWeek = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
    $tabUrl = fn (string $t) => route('business-admin.rota', ['week' => $weekStart, 'tab' => $t]);
@endphp

<x-layouts.business-admin title="Staff Rota" active="rota">
    <div
        x-data="{
            shiftForm: {
                id: null,
                user_id: '',
                user_name: '',
                shift_date: '',
                shift_type: 'Morning',
                rota_section_id: '{{ $sections->first()['id'] ?? '' }}',
                start_time: '09:00',
                end_time: '17:00',
            },
            openShift(userId, userName, date, type, cell) {
                this.shiftForm = {
                    id: cell?.id || null,
                    user_id: userId,
                    user_name: userName,
                    shift_date: date,
                    shift_type: type,
                    rota_section_id: cell?.rota_section_id || '{{ $sections->first()['id'] ?? '' }}',
                    start_time: cell?.start_time || (type === 'Morning' ? '09:00' : '17:00'),
                    end_time: cell?.end_time || (type === 'Morning' ? '17:00' : '23:00'),
                };
                $dispatch('open-modal', 'shift-modal');
            },
            calcHours() {
                if (!this.shiftForm.start_time || !this.shiftForm.end_time) return '0.0';
                const [sh, sm] = this.shiftForm.start_time.split(':').map(Number);
                const [eh, em] = this.shiftForm.end_time.split(':').map(Number);
                let mins = (eh * 60 + em) - (sh * 60 + sm);
                if (mins <= 0) mins += 24 * 60;
                return (mins / 60).toFixed(1);
            },
            isOvernight() {
                return this.shiftForm.end_time && this.shiftForm.start_time && this.shiftForm.end_time <= this.shiftForm.start_time;
            },
        }"
    >
        <x-admin.toolbar title="Rota Management" description="Assign morning and evening shifts with section colours — same layout as Cash Up Pro.">
            <x-admin.nav-pill :href="$tabUrl('weekly')" :active="$tab === 'weekly'">Weekly Rota</x-admin.nav-pill>
            <x-admin.nav-pill :href="$tabUrl('sections')" :active="$tab === 'sections'">Sections</x-admin.nav-pill>
            <x-admin.nav-pill :href="$tabUrl('groups')" :active="$tab === 'groups'">Groups</x-admin.nav-pill>
        </x-admin.toolbar>

        @if ($tab === 'weekly')
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <x-admin.nav-pill :href="route('business-admin.rota', ['week' => $prevWeek, 'tab' => 'weekly'])">← Prev Week</x-admin.nav-pill>
                <h3 class="text-center font-display text-base font-bold sm:text-lg">{{ $weekLabel }}</h3>
                <x-admin.nav-pill :href="route('business-admin.rota', ['week' => $nextWeek, 'tab' => 'weekly'])">Next Week →</x-admin.nav-pill>
            </div>

            @if ($sections->isEmpty())
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Create a section first on the <a href="{{ $tabUrl('sections') }}" class="font-semibold underline">Sections</a> tab, then click a day cell to assign shifts.
                </div>
            @endif

            @foreach ([['title' => 'Morning Shifts', 'type' => 'Morning', 'grid' => $morningGrid, 'header' => 'bg-primary-600'], ['title' => 'Evening Shifts', 'type' => 'Evening', 'grid' => $eveningGrid, 'header' => 'bg-gray-900']] as $block)
                <x-admin.card class="mb-6" :padding="false">
                    <div class="{{ $block['header'] }} px-4 py-3 text-white">
                        <h4 class="font-semibold">{{ $block['title'] }}</h4>
                    </div>
                    <x-admin.matrix-wrap>
                        <table>
                            <thead>
                                <tr>
                                    <th class="admin-matrix-sticky px-3 py-2 text-left">Staff Name</th>
                                    @foreach ($days as $day)
                                        <th class="px-2 py-2 text-center whitespace-nowrap">{{ $day['label'] }}</th>
                                    @endforeach
                                    <th class="px-2 py-2 text-center">Days</th>
                                    <th class="px-2 py-2 text-center">Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($block['grid'] as $row)
                                    <tr class="border-t border-gray-100 dark:border-gray-800">
                                        <td class="admin-matrix-sticky px-3 py-2 font-semibold whitespace-nowrap">{{ $row['name'] }}</td>
                                        @foreach ($row['cells'] as $idx => $cell)
                                            @php $day = $days[$idx]; @endphp
                                            <td class="px-1 py-2 text-center">
                                                <button
                                                    type="button"
                                                    class="admin-touch-target inline-flex min-h-[44px] min-w-[3.5rem] items-center justify-center rounded-lg border border-dashed border-gray-200 px-1.5 py-2 text-[11px] font-semibold dark:border-gray-700 sm:min-w-[4.5rem]"
                                                    @if ($cell)
                                                        style="background: {{ $cell['color'] }}22; border-color: {{ $cell['color'] }}; color: {{ $cell['color'] }};"
                                                    @endif
                                                    @if ($sections->isNotEmpty())
                                                        @click="openShift({{ $row['user_id'] }}, @js($row['name']), @js($day['date']), @js($block['type']), @js($cell))"
                                                    @endif
                                                >
                                                    {{ $cell ? ($cell['start_time'].'-'.$cell['end_time']) : '+' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="px-2 py-2 text-center font-bold">{{ $row['total_days'] }}</td>
                                        <td class="px-2 py-2 text-center font-bold">{{ $row['total_hours'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($days) + 3 }}" class="px-3 py-6 text-sm text-gray-500">No staff yet — add staff, then assign shifts here.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-admin.matrix-wrap>
                </x-admin.card>
            @endforeach
        @endif

        @if ($tab === 'sections')
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">Working Sections</p>
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-section')">Add Section</x-admin.button>
            </div>
            @if ($sections->isEmpty())
                <x-admin.empty-state title="No sections" description="Add sections like Burgers, Fries, Front, Grill." />
            @else
                <div class="admin-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Color</th>
                                    <th class="px-4 py-3">Section Name</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($sections as $section)
                                    <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                        <td class="px-4 py-3.5">
                                            <span class="inline-block h-6 w-6 rounded" style="background: {{ $section['color'] }}"></span>
                                        </td>
                                        <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $section['name'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        @if ($tab === 'groups')
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">Staff Groups</p>
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-group')">Add Group</x-admin.button>
            </div>
            @if ($groups->isEmpty())
                <x-admin.empty-state title="No groups" description="Group staff for quicker rota organisation." />
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($groups as $group)
                        <x-admin.card>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="inline-block h-4 w-4 rounded" style="background: {{ $group['color'] }}"></span>
                                <h4 class="font-semibold">{{ $group['name'] }}</h4>
                            </div>
                            <p class="mb-2 text-xs text-gray-500">Order: {{ $group['display_order'] }}</p>
                            <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                @forelse ($group['users'] as $member)
                                    <li>{{ $member['name'] }}</li>
                                @empty
                                    <li class="text-gray-400">No staff assigned</li>
                                @endforelse
                            </ul>
                        </x-admin.card>
                    @endforeach
                </div>
            @endif
        @endif

        <x-admin.modal name="shift-modal" title="Assign Shift">
            <form method="POST" action="{{ route('business-admin.rota.shifts.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="id" :value="shiftForm.id || ''">
                <input type="hidden" name="user_id" :value="shiftForm.user_id">
                <input type="hidden" name="shift_date" :value="shiftForm.shift_date">
                <input type="hidden" name="shift_type" :value="shiftForm.shift_type">

                <div>
                    <p class="text-sm text-gray-500">Assigning shift for</p>
                    <p class="font-display text-lg font-bold text-primary-700" x-text="(shiftForm.user_name || '') + ' · ' + shiftForm.shift_type + ' · ' + shiftForm.shift_date"></p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Section *</label>
                    <select name="rota_section_id" class="admin-input w-full" x-model="shiftForm.rota_section_id" required>
                        @foreach ($sections as $section)
                            <option value="{{ $section['id'] }}">{{ $section['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">Start *</span>
                        <input type="time" name="start_time" class="admin-input w-full" x-model="shiftForm.start_time" required>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">End *</span>
                        <input type="time" name="end_time" class="admin-input w-full" x-model="shiftForm.end_time" required>
                    </label>
                </div>

                <p class="text-sm text-gray-500">
                    Total Hours: <span class="font-bold" x-text="calcHours()"></span>
                    <span class="ml-2 text-sky-600" x-show="isOvernight()" x-cloak>Shift extends into the next day.</span>
                </p>

                <div class="flex justify-end gap-2">
                    <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save Shift</x-admin.button>
                </div>
            </form>

            <form
                x-show="shiftForm.id"
                x-cloak
                class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-800"
                method="POST"
                :action="`{{ url('/business-admin/rota/shifts') }}/${shiftForm.id}`"
            >
                @csrf
                @method('DELETE')
                <input type="hidden" name="week" value="{{ $weekStart }}">
                <button type="submit" class="text-sm font-semibold text-red-600">Delete Shift</button>
            </form>
        </x-admin.modal>

        <x-admin.modal name="add-section" title="Add Section">
            <form method="POST" action="{{ route('business-admin.rota.sections.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="week" value="{{ $weekStart }}">
                <x-admin.input name="name" label="Section Name" required />
                <label class="block">
                    <span class="mb-1 block text-sm font-medium">Color</span>
                    <input type="color" name="color" value="#563d7c" class="h-10 w-20 cursor-pointer rounded border border-gray-200">
                </label>
                <div class="flex justify-end gap-2">
                    <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save</x-admin.button>
                </div>
            </form>
        </x-admin.modal>

        <x-admin.modal name="add-group" title="Add Group">
            <form method="POST" action="{{ route('business-admin.rota.groups.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="week" value="{{ $weekStart }}">
                <x-admin.input name="name" label="Group Name" required />
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">Color</span>
                        <input type="color" name="color" value="#007bff" class="h-10 w-20 cursor-pointer rounded border border-gray-200">
                    </label>
                    <x-admin.input type="number" name="display_order" label="Display Order" value="0" min="0" />
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium">Assign Staff</p>
                    <div class="max-h-48 space-y-2 overflow-y-auto rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        @foreach ($staff as $member)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="user_ids[]" value="{{ $member['id'] }}" class="rounded border-gray-300 text-primary-600">
                                <span>{{ $member['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save</x-admin.button>
                </div>
            </form>
        </x-admin.modal>
    </div>
</x-layouts.business-admin>
