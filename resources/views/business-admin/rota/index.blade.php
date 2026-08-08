@php
    $prevWeek = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $nextWeek = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
    $tabUrl = fn (string $t) => route('business-admin.rota', ['week' => $weekStart, 'tab' => $t]);
@endphp

<x-layouts.business-admin title="Rota Management" active="rota">
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
        <x-admin.toolbar description="Assign morning and evening shifts with section colours — same layout as Cash Up Pro.">
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

            @if ($draftVersion ?? null)
                <x-admin.card class="mb-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-admin.badge tone="info">Draft v{{ $draftVersion->version_number }}</x-admin.badge>
                                @if ($publishedVersion ?? null)
                                    <x-admin.badge tone="success">Published v{{ $publishedVersion->version_number }}</x-admin.badge>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Staff only see the published version. Your draft changes stay private until you publish.</p>
                            @if (! empty($conflicts))
                                <div class="mt-3 space-y-2">
                                    @foreach ($conflicts as $conflict)
                                        <p class="text-sm {{ ($conflict['severity'] ?? '') === 'error' ? 'text-red-600' : 'text-amber-700' }}">
                                            ⚠ {{ $conflict['message'] }}
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="admin-action-grid shrink-0">
                            <form method="POST" action="{{ route('business-admin.rota.copy-previous-week') }}">
                                @csrf
                                <input type="hidden" name="week" value="{{ $weekStart }}">
                                <x-admin.button type="submit" size="sm" variant="secondary">Copy previous week</x-admin.button>
                            </form>
                            @if ($publishedVersion ?? null)
                                <x-admin.button size="sm" variant="secondary" :href="route('business-admin.rota.print', $publishedVersion)" target="_blank">Print published</x-admin.button>
                            @endif
                            @if ($draftVersion->status->value === 'draft')
                                <form method="POST" action="{{ route('business-admin.rota.finalize', $draftVersion) }}" onsubmit="return confirm('Finalize this rota? It will be reviewed before publishing.');">
                                    @csrf
                                    <input type="hidden" name="confirm" value="1">
                                    <x-admin.button type="submit" size="sm" variant="secondary">Finalize</x-admin.button>
                                </form>
                            @endif
                            @if (in_array($draftVersion->status->value, ['draft', 'finalized'], true))
                                <form method="POST" action="{{ route('business-admin.rota.publish', $draftVersion) }}" onsubmit="return confirm('Publish rota for {{ $weekLabel }}? Staff will see this schedule.');">
                                    @csrf
                                    <input type="hidden" name="confirm" value="1">
                                    <x-admin.button type="submit" size="sm">Publish rota</x-admin.button>
                                </form>
                            @endif
                            @if (($publishedVersion ?? null) && $publishedVersion->status->value === 'published')
                                <form method="POST" action="{{ route('business-admin.rota.lock', $publishedVersion) }}">
                                    @csrf
                                    <x-admin.button type="submit" size="sm" variant="secondary">Lock</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if (! empty($versionChanges))
                        <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Changes from published version</p>
                            <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                @foreach (array_slice($versionChanges, 0, 8) as $change)
                                    <li>{{ $change['user'] }} · {{ $change['day'] }}: {{ $change['before'] }} → {{ $change['after'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </x-admin.card>
            @endif

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
                    <div class="admin-mobile-cards space-y-3 p-4 lg:hidden">
                        @forelse ($block['grid'] as $row)
                            <article class="admin-mobile-card">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $row['name'] }}</h3>
                                <p class="mt-1 text-xs text-gray-500">{{ $row['total_days'] }} days · {{ $row['total_hours'] }} hours</p>
                                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    @foreach ($row['cells'] as $idx => $cell)
                                        @php $day = $days[$idx]; @endphp
                                        <button
                                            type="button"
                                            class="rota-mobile-shift admin-touch-target min-h-[56px] text-left"
                                            @if ($cell)
                                                style="border-color: {{ $cell['color'] }}; background: {{ $cell['color'] }}15;"
                                            @endif
                                            @if ($sections->isNotEmpty())
                                                @click="openShift({{ $row['user_id'] }}, @js($row['name']), @js($day['date']), @js($block['type']), @js($cell))"
                                            @endif
                                        >
                                            <span class="block text-[10px] font-semibold uppercase tracking-wide text-gray-400">{{ $day['label'] }}</span>
                                            @if ($cell)
                                                <span class="mt-1 block text-xs font-semibold">{{ $cell['section'] ?? 'Section' }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $cell['start_time'] }}-{{ $cell['end_time'] }}</span>
                                            @else
                                                <span class="mt-1 block text-sm font-semibold text-gray-400">+ Add</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-gray-500">No staff yet — add staff, then assign shifts here.</p>
                        @endforelse
                    </div>
                    <div class="hidden lg:block">
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
                                                    class="admin-touch-target inline-flex min-h-[44px] min-w-[4.5rem] flex-col items-center justify-center gap-0.5 rounded-lg border border-dashed border-gray-200 px-1.5 py-1.5 text-[10px] font-semibold leading-tight dark:border-gray-700 sm:min-w-[5.5rem]"
                                                    @if ($cell)
                                                        style="background: {{ $cell['color'] }}22; border-color: {{ $cell['color'] }}; color: {{ $cell['color'] }};"
                                                    @endif
                                                    @if ($sections->isNotEmpty())
                                                        @click="openShift({{ $row['user_id'] }}, @js($row['name']), @js($day['date']), @js($block['type']), @js($cell))"
                                                    @endif
                                                >
                                                    @if ($cell)
                                                        <span class="max-w-[5rem] truncate">{{ $cell['section'] ?? 'Section' }}</span>
                                                        <span class="text-[9px] font-medium opacity-90">{{ $cell['start_time'].'-'.$cell['end_time'] }}</span>
                                                    @else
                                                        +
                                                    @endif
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
                    </div>
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
                <x-admin.table-shell>
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Color</th>
                            <th class="px-4 py-3">Section name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                            <tr>
                                <td class="px-4 py-3.5" data-label="Color">
                                    <span class="inline-block h-6 w-6 rounded" style="background: {{ $section['color'] }}"></span>
                                </td>
                                <td class="admin-table-stack-title px-4 py-3.5 text-gray-700 dark:text-gray-200" data-label="Section">{{ $section['name'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table-shell>
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
