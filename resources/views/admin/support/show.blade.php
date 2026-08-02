<x-layouts.admin title="Ticket {{ $ticket->ticket_number }}" active="support">
    <x-admin.breadcrumb :items="['Support', $ticket->ticket_number]" />

    <x-admin.toolbar :title="$ticket->ticket_number" :description="$ticket->subject">
        <x-admin.button variant="secondary" size="sm" :href="route('super-admin.support')">Back</x-admin.button>
        <form method="POST" action="{{ route('super-admin.support.close', $ticket) }}">@csrf<x-admin.button size="sm" variant="danger" type="submit">Close</x-admin.button></form>
    </x-admin.toolbar>

    <div class="grid gap-6 xl:grid-cols-[1fr_20rem]">
        <div class="space-y-6">
            <x-admin.card>
                <h3 class="font-display text-base font-bold">Conversation</h3>
                <p class="mt-3 whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-200">{{ $ticket->body }}</p>
                <ul class="mt-6 space-y-4">
                    @forelse ($ticket->replies as $reply)
                        <li class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                            <p class="text-xs text-gray-500">{{ $reply->user?->name ?? 'System' }} · {{ $reply->created_at?->diffForHumans() }} @if($reply->is_internal)<x-admin.badge tone="warning">Internal</x-admin.badge>@endif</p>
                            <p class="mt-2 whitespace-pre-wrap text-sm">{{ $reply->body }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No replies yet.</li>
                    @endforelse
                </ul>
            </x-admin.card>

            <x-admin.card>
                <h3 class="font-display text-base font-bold">Reply</h3>
                <form method="POST" action="{{ route('super-admin.support.reply', $ticket) }}" class="mt-4 space-y-4">
                    @csrf
                    <x-admin.textarea name="body" label="Message" rows="4" />
                    <x-admin.checkbox name="is_internal" value="1" label="Internal note" />
                    <x-admin.button type="submit">Send reply</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <x-admin.card>
            <h3 class="font-display text-base font-bold">Details</h3>
            <form method="POST" action="{{ route('super-admin.support.update', $ticket) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <x-admin.select name="priority" label="Priority">
                    @foreach (['low','normal','high','urgent'] as $priority)
                        <option value="{{ $priority }}" @selected($ticket->priority->value === $priority)>{{ ucfirst($priority) }}</option>
                    @endforeach
                </x-admin.select>
                <x-admin.select name="status" label="Status">
                    @foreach (['open','pending','closed'] as $status)
                        <option value="{{ $status }}" @selected($ticket->status->value === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </x-admin.select>
                <x-admin.select name="user_id" label="Assign">
                    <option value="">—</option>
                    @foreach (\App\Models\User::query()->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" @selected($ticket->user_id === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </x-admin.select>
                <p class="text-sm text-gray-500">Business: {{ $ticket->organization?->name ?? '—' }}</p>
                <x-admin.button type="submit" size="sm">Save</x-admin.button>
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
