@props([
    'steps' => ['Coins', 'Notes', 'Cards', 'Expenses', 'Online'],
    'currentStep' => 0,
])

@php
    $stepMeta = [
        [
            'label' => 'Coins',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="9" cy="14" r="4"/><circle cx="15" cy="10" r="4"/><path d="M9 10V6M15 14v4"/></svg>',
            'hint' => 'Enter the quantity of each coin to calculate your coins total.',
        ],
        [
            'label' => 'Notes',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M7 10h.01M17 14h.01"/></svg>',
            'hint' => 'Enter the quantity of each note and any extra coin amount.',
        ],
        [
            'label' => 'Cards',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>',
            'hint' => 'Enter card machine totals and any refunds for this shift.',
        ],
        [
            'label' => 'Expenses',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M9 14l2-2 4 4"/><path d="M7 4h10l2 4H5l2-4z"/><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/></svg>',
            'hint' => 'Enter expense descriptions and amounts for this shift.',
        ],
        [
            'label' => 'Online',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/><path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.2 2.6 3.5 5.8 3.5 9s-1.3 6.4-3.5 9c-2.2-2.6-3.5-5.8-3.5-9s1.3-6.4 3.5-9z"/></svg>',
            'hint' => 'Enter the amount for each online order platform.',
        ],
    ];
@endphp

<nav class="cashup-steps-nav" aria-label="Cash up steps">
    <div class="cashup-steps-shell">
        <div class="cashup-steps-header">
            <div>
                <p class="cashup-steps-eyebrow">Cash up workflow</p>
                <p class="cashup-steps-heading">
                    Step <span x-text="step + 1">{{ $currentStep + 1 }}</span>
                    of {{ count($steps) }}
                    ·
                    <span x-text="steps[step]">{{ $steps[$currentStep] ?? '' }}</span>
                </p>
            </div>
            <div class="cashup-steps-shift-total">
                <span class="cashup-steps-shift-label">Shift total</span>
                <span class="cashup-steps-shift-value" x-text="money(shiftTotal)">£0.00</span>
            </div>
        </div>

        <div class="cashup-steps-scroll">
            <ol class="cashup-steps-list" role="tablist">
                @foreach ($stepMeta as $index => $meta)
                    <li class="cashup-steps-item" role="presentation">
                        <button
                            type="button"
                            role="tab"
                            class="cashup-step-tab"
                            :class="{
                                'cashup-step-tab-active': step === {{ $index }},
                                'cashup-step-tab-complete': step > {{ $index }},
                            }"
                            :aria-selected="step === {{ $index }} ? 'true' : 'false'"
                            @if ($currentStep === $index) aria-selected="true" @endif
                            @click="goToStep({{ $index }})"
                        >
                            <span class="cashup-step-icon-wrap">
                                <span class="cashup-step-icon">{!! $meta['icon'] !!}</span>
                                <span class="cashup-step-check" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </span>
                            </span>
                            <span class="cashup-step-copy">
                                <span class="cashup-step-label">{{ $meta['label'] }}</span>
                                <span class="cashup-step-total" x-text="stepTabTotal({{ $index }})">
                                    £0.00
                                </span>
                            </span>
                        </button>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="cashup-steps-progress" aria-hidden="true">
            <div
                class="cashup-steps-progress-bar"
                :style="`width: ${((step + 1) / steps.length) * 100}%`"
                style="width: {{ (($currentStep + 1) / max(count($steps), 1)) * 100 }}%"
            ></div>
        </div>
    </div>

    <div class="cashup-step-intro">
        <span class="cashup-step-intro-badge" x-text="step + 1">{{ $currentStep + 1 }}</span>
        <p class="cashup-step-intro-text" x-text="stepDescriptions[step]">
            {{ $stepMeta[$currentStep]['hint'] ?? '' }}
        </p>
    </div>
</nav>
