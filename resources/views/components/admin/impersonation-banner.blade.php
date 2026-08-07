@props([
    'impersonator' => null,
    'organizationName' => null,
    'startedAt' => null,
    'reason' => null,
])

@if ($impersonator)
    <div
        class="sticky top-0 z-[60] border-b border-amber-300 bg-amber-100 px-4 py-2.5 text-sm text-amber-950 dark:border-amber-800 dark:bg-amber-950/90 dark:text-amber-100"
        role="status"
        aria-live="polite"
    >
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="font-semibold">Impersonation active</p>
                <p class="mt-0.5 text-xs sm:text-sm">
                    Super Admin: <span class="font-medium">{{ $impersonator->name }}</span>
                    · Business: <span class="font-medium">{{ $organizationName }}</span>
                    @if ($startedAt)
                        · Started {{ \Illuminate\Support\Carbon::parse($startedAt)->diffForHumans() }}
                    @endif
                    @if ($reason)
                        · Reason: {{ $reason }}
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('impersonation.stop') }}" class="shrink-0">
                @csrf
                <button
                    type="submit"
                    class="inline-flex min-h-[40px] items-center justify-center rounded-xl bg-amber-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-950 dark:bg-amber-800 dark:hover:bg-amber-700"
                >
                    Exit impersonation
                </button>
            </form>
        </div>
    </div>
@endif
