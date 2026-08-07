@props(['active' => 'dashboard'])

@php
    $featureAccess = app(\App\Services\Billing\FeatureAccessService::class);
    $user = auth()->user();

    $groups = [
        [
            'key' => 'overview',
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'business-admin.finance.dashboard', 'key' => 'dashboard'],
                ['label' => 'Reports', 'route' => 'business-admin.finance.reports', 'key' => 'reports'],
            ],
        ],
        [
            'key' => 'money',
            'label' => 'Money in & out',
            'items' => [
                ['label' => 'Income', 'route' => 'business-admin.finance.income', 'key' => 'income'],
                ['label' => 'Expenses', 'route' => 'business-admin.finance.expenses', 'key' => 'expenses'],
                ['label' => 'Cash flow', 'route' => 'business-admin.finance.cash-flow', 'key' => 'cash-flow'],
            ],
        ],
        [
            'key' => 'payables',
            'label' => 'Payables',
            'items' => [
                ['label' => 'Bills', 'route' => 'business-admin.finance.bills', 'key' => 'bills'],
                ['label' => 'Recurring bills', 'route' => 'business-admin.finance.recurring-bills', 'key' => 'recurring-bills'],
                ['label' => 'Purchase invoices', 'route' => 'business-admin.finance.purchase-invoices', 'key' => 'purchase-invoices'],
                ['label' => 'Supplier payments', 'route' => 'business-admin.finance.supplier-payments', 'key' => 'supplier-payments'],
            ],
        ],
        [
            'key' => 'payroll',
            'label' => 'Payroll',
            'feature' => 'payroll',
            'items' => [
                ['label' => 'Payroll', 'route' => 'business-admin.finance.payroll', 'key' => 'payroll', 'feature' => 'payroll'],
                ['label' => 'Weekly wages', 'route' => 'business-admin.finance.weekly-wages', 'key' => 'weekly-wages', 'feature' => 'payroll'],
            ],
        ],
        [
            'key' => 'insights',
            'label' => 'Insights',
            'items' => [
                ['label' => 'Profit & loss', 'route' => 'business-admin.finance.profit-loss', 'key' => 'profit-loss'],
                ['label' => 'VAT summary', 'route' => 'business-admin.finance.vat', 'key' => 'vat'],
            ],
        ],
        [
            'key' => 'setup',
            'label' => 'Setup',
            'items' => [
                ['label' => 'Bank accounts', 'route' => 'business-admin.finance.bank-accounts', 'key' => 'bank-accounts'],
                ['label' => 'Cash drawers', 'route' => 'business-admin.finance.cash-drawers', 'key' => 'cash-drawers'],
                ['label' => 'Petty cash', 'route' => 'business-admin.finance.petty-cash', 'key' => 'petty-cash'],
            ],
        ],
    ];

    $visibleGroups = collect($groups)
        ->map(function (array $group) use ($featureAccess, $user) {
            if (isset($group['feature']) && ($user === null || ! $featureAccess->can($user, $group['feature']))) {
                return null;
            }

            $group['items'] = array_values(array_filter(
                $group['items'],
                fn (array $item) => ! isset($item['feature']) || ($user !== null && $featureAccess->can($user, $item['feature'])),
            ));

            return $group['items'] !== [] ? $group : null;
        })
        ->filter()
        ->values()
        ->all();

    $matchedGroup = collect($visibleGroups)
        ->first(fn (array $group) => collect($group['items'])->contains(fn (array $item) => $item['key'] === $active));

    $activeGroupKey = $matchedGroup['key'] ?? ($visibleGroups[0]['key'] ?? 'overview');

    $activeGroup = collect($visibleGroups)->firstWhere('key', $activeGroupKey) ?? $visibleGroups[0];
@endphp

<nav class="finance-subnav admin-card mb-6" aria-label="Finance sections">
    {{-- Mobile: grouped jump menu --}}
    <div class="finance-subnav-mobile border-b border-gray-100 p-3 dark:border-gray-800 lg:hidden">
        <label for="finance-section-jump" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            Finance section
        </label>
        <select
            id="finance-section-jump"
            class="admin-input w-full"
            onchange="if (this.value) window.location.href = this.value"
        >
            @foreach ($visibleGroups as $group)
                <optgroup label="{{ $group['label'] }}">
                    @foreach ($group['items'] as $item)
                        <option value="{{ route($item['route']) }}" @selected($active === $item['key'])>
                            {{ $item['label'] }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    {{-- Desktop: group tabs + section links --}}
    <div class="hidden lg:block">
        <div class="finance-subnav-groups flex flex-wrap gap-1 border-b border-gray-100 p-2 dark:border-gray-800">
            @foreach ($visibleGroups as $group)
                @php
                    $groupActive = $activeGroupKey === $group['key'];
                    $groupHref = route($group['items'][0]['route']);
                @endphp
                <a
                    href="{{ $groupHref }}"
                    @class([
                        'finance-subnav-group',
                        'finance-subnav-group-active' => $groupActive,
                    ])
                    @if ($groupActive) aria-current="true" @endif
                >
                    {{ $group['label'] }}
                </a>
            @endforeach
        </div>

        <div class="finance-subnav-links flex flex-wrap gap-1 p-2">
            @foreach ($activeGroup['items'] as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'finance-subnav-link',
                        'finance-subnav-link-active' => $active === $item['key'],
                    ])
                    @if ($active === $item['key']) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tablet: compact grouped grid --}}
    <div class="hidden md:grid md:grid-cols-2 md:gap-px md:bg-gray-100 lg:hidden dark:md:bg-gray-800">
        @foreach ($visibleGroups as $group)
            <div class="bg-white p-3 dark:bg-gray-900">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    {{ $group['label'] }}
                </p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @class([
                                    'finance-subnav-link finance-subnav-link-block',
                                    'finance-subnav-link-active' => $active === $item['key'],
                                ])
                                @if ($active === $item['key']) aria-current="page" @endif
                            >
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</nav>
