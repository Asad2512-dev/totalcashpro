        <div class="mt-16 space-y-3 sm:hidden" data-reveal>
            @foreach ($pricingComparison as $row)
                <article class="rounded-2xl border border-line bg-white p-4 shadow-sm">
                    <h3 class="font-display text-sm font-bold text-navy-900">{{ $row['feature'] }}</h3>
                    <dl class="mt-3 grid grid-cols-3 gap-2 text-center">
                        @foreach (['basic' => 'Basic', 'professional' => 'Pro', 'enterprise' => 'Ent.'] as $plan => $planLabel)
                            @php $val = $row[$plan]; @endphp
                            <div class="rounded-xl border border-line/80 bg-navy-50/40 px-2 py-2.5">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-mute">{{ $planLabel }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-navy-800">
                                    @if ($val === true)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-label="Included">✓</span>
                                    @elseif ($val === false)
                                        <span class="text-mute" aria-label="Not included">—</span>
                                    @else
                                        {{ $val }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </article>
            @endforeach
        </div>

        <div class="mt-16 hidden overflow-x-auto sm:block" data-reveal>
            <table class="w-full min-w-[640px] border-collapse text-left text-sm">
                <caption class="sr-only">Feature comparison across Basic, Professional and Enterprise plans</caption>
                <thead>
                    <tr class="border-b border-line">
                        <th scope="col" class="py-3 pr-4 font-display font-bold text-navy-900">Feature</th>
                        <th scope="col" class="px-4 py-3 text-center font-display font-bold text-navy-900">Basic</th>
                        <th scope="col" class="px-4 py-3 text-center font-display font-bold text-emerald-700">Professional</th>
                        <th scope="col" class="px-4 py-3 text-center font-display font-bold text-navy-600">Enterprise</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($pricingComparison as $row)
                        <tr>
                            <th scope="row" class="py-3 pr-4 font-medium text-navy-800">{{ $row['feature'] }}</th>
                            @foreach (['basic', 'professional', 'enterprise'] as $plan)
                                <td class="px-4 py-3 text-center">
                                    @php $val = $row[$plan]; @endphp
                                    @if ($val === true)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-label="Included">✓</span>
                                    @elseif ($val === false)
                                        <span class="text-mute" aria-label="Not included">—</span>
                                    @else
                                        <span class="text-xs font-semibold text-navy-700">{{ $val }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
