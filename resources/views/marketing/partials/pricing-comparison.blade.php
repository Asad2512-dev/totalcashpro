        <div class="mt-16 overflow-x-auto" data-reveal>
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
