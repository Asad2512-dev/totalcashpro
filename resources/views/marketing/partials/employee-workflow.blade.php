<section id="employee-workflow" class="scroll-mt-24 bg-emerald-50/50 py-20 lg:py-28 dark:bg-emerald-950/20" aria-labelledby="employee-workflow-heading">
    <x-container>
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-800 ring-1 ring-inset ring-emerald-200">
                Daily Workflow
            </span>
            <h2 id="employee-workflow-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-4xl">
                From clock-in to payroll to finance
            </h2>
            <p class="mt-4 text-base leading-relaxed text-mute sm:text-lg">
                See how attendance flows through TotalCashPro — from the kiosk at your door to reports your accountant needs.
            </p>
        </div>

        <ol class="relative mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach ($employeeWorkflow as $index => $item)
                <li class="relative rounded-2xl border border-line bg-snow p-5 shadow-sm" data-reveal>
                    @if ($index < count($employeeWorkflow) - 1)
                        <span class="pointer-events-none absolute -right-3 top-1/2 hidden h-px w-6 bg-emerald-300 xl:block" aria-hidden="true"></span>
                    @endif
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">{{ $item['step'] }}</span>
                    <h3 class="mt-3 font-display text-sm font-bold text-navy-900">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-xs leading-relaxed text-mute">{{ $item['description'] }}</p>
                </li>
            @endforeach
        </ol>
    </x-container>
</section>
