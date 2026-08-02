<x-layouts.auth title="Welcome setup" :seo="['title' => 'Welcome — TotalCashPro']">
    <div class="min-h-screen bg-gray-50 px-6 py-10">
        <div class="mx-auto max-w-2xl">
            @if (auth()->user() && ! auth()->user()->hasVerifiedEmail())
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Please verify your email.
                    <a href="{{ route('verification.notice') }}" class="font-semibold underline">Resend link</a>
                </div>
            @endif

            @if ($trial)
                <div class="mb-6 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-900">
                    Your <strong>14-day Professional trial</strong> is active
                    @if ($trial['days_remaining'])
                        — {{ $trial['days_remaining'] }} day{{ $trial['days_remaining'] === 1 ? '' : 's' }} remaining.
                    @endif
                </div>
            @endif

            <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
                <div class="mb-8 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-600">Welcome wizard</p>
                        <h1 class="mt-2 font-display text-2xl font-extrabold text-gray-900">Step {{ $step }} of 5</h1>
                    </div>
                    <form method="POST" action="{{ route('business-admin.onboarding.skip') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-800">Skip for now</button>
                    </form>
                </div>

                <div class="mb-8 flex gap-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                    @endfor
                </div>

                <form method="POST" action="{{ route('business-admin.onboarding.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="step" value="{{ $step }}">

                    @if ($step === 1)
                        <h2 class="text-lg font-bold text-gray-900">Welcome to {{ brand_name() }}</h2>
                        <p class="text-sm text-gray-600">{{ $organization?->name }} is ready. We created your Main Branch and started your Professional trial automatically.</p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>✓ Organisation created</li>
                            <li>✓ Business Admin account assigned</li>
                            <li>✓ Main Branch ready</li>
                            <li>✓ Professional trial active</li>
                        </ul>
                    @elseif ($step === 2)
                        <h2 class="text-lg font-bold text-gray-900">Business information</h2>
                        <div>
                            <label class="text-sm font-semibold">Business name</label>
                            <input name="business_name" value="{{ old('business_name', $organization?->name) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Phone</label>
                            <input name="phone" value="{{ old('phone', $organization?->phone) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Tax / VAT number (optional)</label>
                            <input name="tax_number" value="{{ old('tax_number', $organization?->tax_number) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                    @elseif ($step === 3)
                        <h2 class="text-lg font-bold text-gray-900">Branch information</h2>
                        <div>
                            <label class="text-sm font-semibold">Branch name</label>
                            <input name="branch_name" value="{{ old('branch_name', $branch?->name) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">City</label>
                            <input name="city" value="{{ old('city', $branch?->city) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Address</label>
                            <input name="address" value="{{ old('address', $branch?->address) }}" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm">
                        </div>
                    @elseif ($step === 4)
                        <h2 class="text-lg font-bold text-gray-900">Invite staff (optional)</h2>
                        <p class="text-sm text-gray-600">Add staff emails separated by commas. You can add team members later from Staff.</p>
                        <textarea name="staff_invites" rows="4" class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm" placeholder="staff1@example.com, staff2@example.com">{{ old('staff_invites') }}</textarea>
                    @else
                        <h2 class="text-lg font-bold text-gray-900">Finish setup</h2>
                        <p class="text-sm text-gray-600">You're all set. Click finish to open your Business Admin dashboard.</p>
                        <input type="hidden" name="business_name" value="{{ old('business_name', $organization?->name) }}">
                        <input type="hidden" name="phone" value="{{ old('phone', $organization?->phone) }}">
                        <input type="hidden" name="branch_name" value="{{ old('branch_name', $branch?->name) }}">
                    @endif

                    <div class="flex gap-3 pt-2">
                        @if ($step > 1)
                            <a href="{{ route('business-admin.onboarding', ['step' => $step - 1]) }}" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700">Back</a>
                        @endif
                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white hover:bg-primary-700">
                            {{ $step === 5 ? 'Finish setup' : 'Continue' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>
