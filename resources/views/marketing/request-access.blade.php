<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-10">
        <x-container class="max-w-3xl">
            <x-badge>Request Access</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Request your TotalCashPro account
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-mute">
                There is no instant registration. Submit your business details, and our team will review your request, create your organisation, and email your login credentials.
            </p>
        </x-container>
    </section>

    <section class="pb-24">
        <x-container>
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14">
                <aside class="space-y-6" data-reveal>
                    <div class="rounded-[1.75rem] border border-line bg-snow p-7">
                        <h2 class="font-display text-xl font-bold text-navy-900">What happens next</h2>
                        <ol class="mt-6 space-y-4 text-sm text-mute">
                            <li class="flex gap-3">
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-royal-50 text-xs font-bold text-royal-700">1</span>
                                <span>Choose Basic or Professional and submit this form.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-royal-50 text-xs font-bold text-royal-700">2</span>
                                <span>Our super admin reviews your business request.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-royal-50 text-xs font-bold text-royal-700">3</span>
                                <span>We create your organisation and admin account.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-royal-50 text-xs font-bold text-royal-700">4</span>
                                <span>You receive login credentials by email.</span>
                            </li>
                        </ol>
                    </div>

                    <div class="rounded-[1.75rem] border border-line bg-navy-900 p-7 text-white">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-sky-400">Plans</p>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <p class="font-semibold">Basic — £19.99/month</p>
                                <p class="mt-1 text-white/65">Cash up, staff tools and daily reports for smaller teams.</p>
                            </div>
                            <div>
                                <p class="font-semibold">Professional — £29.99/month</p>
                                <p class="mt-1 text-white/65">Everything in Basic plus inventory, payroll and multiple branches.</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="rounded-[1.75rem] border border-line bg-snow p-6 shadow-soft sm:p-8" data-reveal>
                    <form method="POST" action="{{ route('request-access.store') }}" class="space-y-5">
                        @csrf

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="business_name" class="text-sm font-semibold text-navy-900">Business Name</label>
                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('business_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="owner_name" class="text-sm font-semibold text-navy-900">Owner Name</label>
                                <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('owner_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="text-sm font-semibold text-navy-900">Email Address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="text-sm font-semibold text-navy-900">Phone</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="country" class="text-sm font-semibold text-navy-900">Country</label>
                                <input id="country" name="country" type="text" value="{{ old('country') }}" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('country') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="business_address" class="text-sm font-semibold text-navy-900">Business Address</label>
                                <input id="business_address" name="business_address" type="text" value="{{ old('business_address') }}" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                @error('business_address') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="business_type" class="text-sm font-semibold text-navy-900">Business Type</label>
                                <select id="business_type" name="business_type" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                    <option value="">Select type</option>
                                    @foreach ($businessTypes as $type)
                                        <option value="{{ $type }}" @selected(old('business_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('business_type') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="number_of_employees" class="text-sm font-semibold text-navy-900">Number of Employees</label>
                                <select id="number_of_employees" name="number_of_employees" required class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">
                                    <option value="">Select range</option>
                                    @foreach ($employeeRanges as $range)
                                        <option value="{{ $range }}" @selected(old('number_of_employees') === $range)>{{ $range }}</option>
                                    @endforeach
                                </select>
                                @error('number_of_employees') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-sm font-semibold text-navy-900">Select Plan</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    @foreach ($plans as $plan)
                                        <label @class([
                                            'flex cursor-pointer flex-col rounded-2xl border px-4 py-4 transition',
                                            'border-royal-600 bg-royal-50 ring-2 ring-royal-600/20' => old('selected_plan', $selectedPlan) === $plan->value,
                                            'border-line bg-paper hover:border-royal-600/40' => old('selected_plan', $selectedPlan) !== $plan->value,
                                        ])>
                                            <span class="flex items-center gap-2">
                                                <input
                                                    type="radio"
                                                    name="selected_plan"
                                                    value="{{ $plan->value }}"
                                                    class="text-royal-600 focus:ring-royal-600"
                                                    @checked(old('selected_plan', $selectedPlan) === $plan->value)
                                                    required
                                                >
                                                <span class="font-semibold text-navy-900">{{ $plan->label() }}</span>
                                            </span>
                                            <span class="mt-2 pl-6 text-sm text-mute">{{ $plan->priceLabel() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selected_plan') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="additional_notes" class="text-sm font-semibold text-navy-900">Additional Notes</label>
                                <textarea id="additional_notes" name="additional_notes" rows="4" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20">{{ old('additional_notes') }}</textarea>
                                @error('additional_notes') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn-ripple inline-flex w-full items-center justify-center rounded-2xl bg-royal-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-royal-800"
                        >
                            Submit Request
                        </button>

                        <p class="text-center text-sm leading-relaxed text-mute">
                            Our team will review your request and contact you within 24 hours.
                        </p>
                    </form>
                </div>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
