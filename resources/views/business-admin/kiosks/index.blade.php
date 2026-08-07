<x-layouts.business-admin title="Smart Kiosks" active="kiosks">
    <x-admin.toolbar description="Create and manage token-based attendance kiosks — multiple kiosks per branch are supported.">
        <x-slot:actions>
            @if ($branches->isNotEmpty())
                <x-admin.button type="button" variant="primary" @click="$dispatch('open-modal', 'create-kiosk')">
                    <x-admin.icon name="plus" class="h-4 w-4" />
                    Create Kiosk
                </x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert type="success" class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif

    @if ($kiosks->isEmpty())
        <x-admin.card>
            <x-admin.empty-state
                title="No kiosks yet"
                description="Create smart kiosks for your branches — e.g. front entrance, kitchen, or back office. Each gets its own secure URL."
            />
            @if ($branches->isNotEmpty())
                <div class="mt-6 flex justify-center border-t border-gray-100 pt-6 dark:border-gray-800">
                    <x-admin.button type="button" variant="primary" @click="$dispatch('open-modal', 'create-kiosk')">Create Your First Kiosk</x-admin.button>
                </div>
            @else
                <p class="mt-4 text-center text-sm text-gray-500">Add a branch first under <a href="{{ route('business-admin.branches') }}" class="font-semibold text-primary-600 hover:underline">Branches</a>, then return here to create a kiosk.</p>
            @endif
        </x-admin.card>
    @else
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($kiosks as $kiosk)
                <x-admin.card>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $kiosk->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $kiosk->branch->name }}</p>
                            @if ($kiosk->activeSession)
                                <span class="mt-2 inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Session active</span>
                            @else
                                <span class="mt-2 inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">Offline</span>
                            @endif
                            @if (! $kiosk->is_enabled)
                                <span class="mt-2 inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">Disabled</span>
                            @endif
                        </div>
                        <x-admin.table-action :href="route('business-admin.kiosks.activity', $kiosk)" variant="neutral">Activity</x-admin.table-action>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-900/50" x-data="copyText(@js($kiosk->publicUrl()))">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kiosk URL</p>
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-primary-700 dark:hover:bg-primary-950/40 dark:hover:text-primary-300"
                                @click="copy()"
                            >
                                <svg x-show="!copied" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                            </button>
                        </div>
                        <textarea
                            readonly
                            rows="3"
                            @click="copy()"
                            title="Click to copy kiosk URL"
                            class="mt-2 w-full min-h-[4.5rem] cursor-pointer resize-none break-all rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-[11px] leading-relaxed text-gray-800 outline-none transition hover:border-primary-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-primary-700"
                        >{{ $kiosk->publicUrl() }}</textarea>
                        <p class="mt-1.5 text-xs text-gray-500">Click the URL or Copy link to copy to clipboard.</p>
                    </div>

                    <form method="POST" action="{{ route('business-admin.kiosks.update', $kiosk) }}" class="mt-4 space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="admin-label">Name</label>
                            <input type="text" name="name" value="{{ $kiosk->name }}" class="admin-input mt-1 w-full" required>
                        </div>
                        <div>
                            <label class="admin-label">Welcome message</label>
                            <input type="text" name="welcome_message" value="{{ $kiosk->welcome_message }}" class="admin-input mt-1 w-full" required>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="show_photos" value="1" @checked($kiosk->show_photos)>
                            Show staff photos
                        </label>
                        <x-admin.button type="submit" variant="secondary" size="sm">Save</x-admin.button>
                    </form>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <x-admin.button href="{{ $kiosk->publicUrl() }}" variant="secondary" size="sm" target="_blank" rel="noopener">
                            Open Kiosk
                        </x-admin.button>
                        <div x-data="copyText(@js($kiosk->publicUrl()))" class="inline-flex">
                            <x-admin.button type="button" variant="secondary" size="sm" @click="copy()" x-bind:aria-label="copied ? 'URL copied' : 'Copy kiosk URL'">
                                <span x-text="copied ? 'Copied!' : 'Copy URL'"></span>
                            </x-admin.button>
                        </div>
                        <form method="POST" action="{{ route('business-admin.kiosks.regenerate-token', $kiosk) }}" class="inline-flex" onsubmit="return confirm('Regenerate token? Old URLs will stop working.')">
                            @csrf
                            <x-admin.button type="submit" variant="secondary" size="sm">Regenerate Token</x-admin.button>
                        </form>
                        @if ($kiosk->activeSession)
                            <form method="POST" action="{{ route('business-admin.kiosks.force-logout', $kiosk) }}" class="inline-flex">
                                @csrf
                                <x-admin.button type="submit" variant="secondary" size="sm">Force Logout</x-admin.button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('business-admin.kiosks.reset', $kiosk) }}" class="inline-flex" onsubmit="return confirm('Reset kiosk settings and end sessions?')">
                            @csrf
                            <x-admin.button type="submit" variant="secondary" size="sm">Reset</x-admin.button>
                        </form>
                        @if ($kiosk->is_enabled)
                            <form method="POST" action="{{ route('business-admin.kiosks.disable', $kiosk) }}" class="inline-flex">
                                @csrf
                                <x-admin.button type="submit" variant="danger" size="sm">Disable</x-admin.button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('business-admin.kiosks.enable', $kiosk) }}" class="inline-flex">
                                @csrf
                                <x-admin.button type="submit" variant="secondary" size="sm">Enable</x-admin.button>
                            </form>
                        @endif
                    </div>
                </x-admin.card>
            @endforeach
        </div>
    @endif

    @if ($branches->isNotEmpty())
        <x-admin.modal name="create-kiosk" title="Create Smart Kiosk">
            <form method="POST" action="{{ route('business-admin.kiosks.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="admin-label" for="branch_id">Branch</label>
                    <select id="branch_id" name="branch_id" class="admin-input mt-1 w-full" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }}@if ($branch->kiosks_count > 0) ({{ $branch->kiosks_count }} {{ Str::plural('kiosk', $branch->kiosks_count) }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label" for="name">Kiosk name</label>
                    <input type="text" id="name" name="name" class="admin-input mt-1 w-full" placeholder="e.g. Front Entrance, Kitchen, Back Office">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to auto-name (e.g. Dockside Kiosk 2).</p>
                </div>
                <x-admin.button type="submit" variant="primary" class="w-full justify-center">Create Kiosk</x-admin.button>
            </form>
        </x-admin.modal>
    @endif
</x-layouts.business-admin>
