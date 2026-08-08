@php
    $existingCoins = collect($cashUp?->coins_detail ?? [])->keyBy('coin');
    $existingNotes = collect($cashUp?->notes_detail ?? [])->keyBy('note');
    $existingCards = collect($cashUp?->cards_detail ?? [])->values();
    $existingExpenses = collect($cashUp?->expenses_detail ?? [])->values();
    $existingOnline = collect($cashUp?->online_orders_detail ?? [])->keyBy('platform');
    $existingDeductions = collect($cashUp?->platform_deductions_detail ?? [])->keyBy('platform');

    $coinRows = collect($coins)->map(fn ($c) => [
        'coin' => $c['coin'],
        'value' => (float) $c['value'],
        'qty' => (int) ($existingCoins[$c['coin']]['qty'] ?? 0),
    ])->values()->all();

    $noteRows = collect($notes)->map(fn ($n) => [
        'note' => $n['note'],
        'value' => (float) $n['value'],
        'is_qty' => (bool) $n['is_qty'],
        'qty' => $n['is_qty'] ? (int) ($existingNotes[$n['note']]['qty'] ?? 0) : 0,
        'amount' => ! $n['is_qty'] ? (float) ($existingNotes[$n['note']]['amount'] ?? 0) : 0,
    ])->values()->all();

    $cardRows = ($existingCards->isEmpty()
        ? collect([['payment_type' => 'Card Machine 1', 'type' => 'machine', 'amount' => 0]])
        : $existingCards->where('type', '!=', 'refund')->values()
    )->map(fn ($c) => [
        'payment_type' => $c['payment_type'] ?? 'Card Machine',
        'type' => 'machine',
        'amount' => (float) ($c['amount'] ?? 0),
    ])->values()->all();

    $expenseRows = ($existingExpenses->isEmpty()
        ? collect([['description' => '', 'amount' => 0]])
        : $existingExpenses
    )->map(fn ($e) => [
        'description' => $e['description'] ?? '',
        'amount' => (float) ($e['amount'] ?? 0),
    ])->values()->all();

    $platformRows = collect($platforms)->map(fn ($p) => [
        'platform' => $p,
        'amount' => (float) ($existingOnline[$p]['amount'] ?? 0),
    ])->values()->all();

    $deductionRows = collect($platforms)->map(fn ($p) => [
        'platform' => $p,
        'amount' => (float) ($existingDeductions[$p]['amount'] ?? 0),
    ])->values()->all();

    $stepLabels = ['Coins', 'Notes', 'Cards', 'Expenses', 'Online'];
    $step = max(0, min(4, (int) request('step', 0)));
    $wizardConfig = [
        'saveUrl' => route('staff.cash-up.store', absolute: false),
        'deductionsUrl' => route('staff.cash-up.deductions', absolute: false),
        'csrf' => csrf_token(),
        'initialDate' => $date,
        'initialShift' => $shift,
        'initialStep' => $step,
        'coins' => $coinRows,
        'notes' => $noteRows,
        'cards' => $cardRows,
        'refundAmount' => (float) ($existingCards->firstWhere('type', 'refund')['amount'] ?? 0),
        'expenses' => $expenseRows,
        'platforms' => $platformRows,
        'deductions' => $deductionRows,
    ];
@endphp

<x-layouts.staff title="Cash Up" active="cash-up">
    <script type="application/json" id="cashup-wizard-config">@json($wizardConfig)</script>

    <div class="cashup-page mx-auto max-w-5xl" x-data="cashUpWizard(JSON.parse(document.getElementById('cashup-wizard-config').textContent))">
        <x-admin.card class="cashup-widget" :padding="false">
            <div class="border-b border-gray-200 px-5 pb-4 pt-5 dark:border-gray-700 sm:px-6">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">Daily Cash Up</h2>
                        <p class="mt-1 text-sm text-gray-500">Same Cash Up Pro flow — coins, notes, cards, expenses, online & deductions.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('staff.cash-up', ['date' => $date, 'shift' => $shift, 'view' => 'cashup', 'step' => $step]) }}" @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-primary-600 text-white' => $viewTab === 'cashup', 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' => $viewTab !== 'cashup'])>Cash Up</a>
                        <a href="{{ route('staff.cash-up', ['date' => $date, 'shift' => $shift, 'view' => 'deductions']) }}" @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-primary-600 text-white' => $viewTab === 'deductions', 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' => $viewTab !== 'deductions'])>Platform Deduction</a>
                        <a href="{{ route('staff.dashboard') }}" class="ml-1 text-sm font-semibold text-primary-700 hover:underline">Dashboard →</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('staff.cash-up') }}" class="admin-field-grid max-w-xl sm:grid-cols-2">
                    <input type="hidden" name="view" value="{{ $viewTab }}">
                    <input type="hidden" name="step" value="{{ $step }}">
                    <label class="admin-field">
                        <span class="admin-label">Date</span>
                        <input type="date" name="date" value="{{ $date }}" class="admin-input" x-model="date" @change="reloadForDate()">
                    </label>
                    <label class="admin-field">
                        <span class="admin-label">Shift</span>
                        <select name="shift" class="admin-input" x-model="shift" @change="reloadForDate()">
                            <option value="Morning" @selected($shift === 'Morning')>Morning</option>
                            <option value="Evening" @selected($shift === 'Evening')>Evening</option>
                        </select>
                    </label>
                    <noscript>
                        <div class="col-span-2">
                            <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Apply</button>
                        </div>
                    </noscript>
                </form>
            </div>

            <div class="px-5 py-5 sm:px-6">
                <p class="mb-4 min-h-[1.25rem] text-sm" :class="error ? 'text-red-600' : 'text-primary-700'" x-text="statusMessage"></p>

                @if ($viewTab === 'cashup')
                    <x-cashup.steps-nav :steps="$stepLabels" :current-step="$step" />

                    {{-- Coins --}}
                    <div x-show="step === 0" @if ($step !== 0) x-cloak style="display:none" @endif>
                        <table class="cashup-table">
                            <thead>
                                <tr>
                                    <th class="cashup-col-label">Coin</th>
                                    <th class="cashup-col-input">Quantity</th>
                                    <th class="cashup-col-total">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coinRows as $idx => $row)
                                    <tr>
                                        <td class="font-medium text-gray-900 dark:text-white">{{ $row['coin'] }}</td>
                                        <td>
                                            <input type="number" min="0" step="1" class="cashup-input" placeholder="0" value="{{ $row['qty'] }}" x-model.number="coins[{{ $idx }}].qty">
                                        </td>
                                        <td class="cashup-total" x-text="money(coins[{{ $idx }}].value * (coins[{{ $idx }}].qty || 0))">£{{ number_format($row['value'] * $row['qty'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-bold">Coins Grand Total</td>
                                    <td class="cashup-total font-bold" x-text="money(coinsTotal)">£{{ number_format(collect($coinRows)->sum(fn ($r) => $r['value'] * $r['qty']), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Notes --}}
                    <div x-show="step === 1" @if ($step !== 1) x-cloak style="display:none" @endif>
                        <table class="cashup-table">
                            <thead>
                                <tr>
                                    <th class="cashup-col-label">Note / Float</th>
                                    <th class="cashup-col-input">Quantity / Amount</th>
                                    <th class="cashup-col-total">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($noteRows as $idx => $row)
                                    <tr>
                                        <td class="font-medium text-gray-900 dark:text-white">{{ $row['note'] }}</td>
                                        <td>
                                            @if ($row['is_qty'])
                                                <input type="number" min="0" step="1" class="cashup-input" placeholder="0" value="{{ $row['qty'] }}" x-model.number="notes[{{ $idx }}].qty">
                                            @else
                                                <input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $row['amount'] }}" x-model.number="notes[{{ $idx }}].amount">
                                            @endif
                                        </td>
                                        <td class="cashup-total" x-text="money(notes[{{ $idx }}].is_qty ? notes[{{ $idx }}].value * (notes[{{ $idx }}].qty || 0) : (notes[{{ $idx }}].amount || 0))">
                                            £{{ number_format($row['is_qty'] ? $row['value'] * $row['qty'] : $row['amount'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-bold">Notes Grand Total</td>
                                    <td class="cashup-total font-bold" x-text="money(notesTotal)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Cards --}}
                    <div x-show="step === 2" @if ($step !== 2) x-cloak style="display:none" @endif>
                        <div class="cashup-table overflow-hidden">
                            <div class="cashup-row cashup-head">
                                <div class="cashup-col-label">Payment Type</div>
                                <div class="cashup-col-input">Amount</div>
                                <div class="cashup-col-total">Total Amount (£)</div>
                            </div>
                            @foreach ($cardRows as $idx => $row)
                                <div class="cashup-row">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $row['payment_type'] }}</div>
                                    <div><input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $row['amount'] }}" x-model.number="cards[{{ $idx }}].amount"></div>
                                    <div class="cashup-total" x-text="money(cards[{{ $idx }}].amount || 0)">£{{ number_format($row['amount'], 2) }}</div>
                                </div>
                            @endforeach
                            <template x-for="(row, idx) in cards.slice({{ count($cardRows) }})" :key="'card-extra-'+idx">
                                <div class="cashup-row">
                                    <div class="font-medium text-gray-900 dark:text-white" x-text="row.payment_type"></div>
                                    <div><input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" x-model.number="cards[{{ count($cardRows) }} + idx].amount"></div>
                                    <div class="cashup-total" x-text="money(row.amount || 0)"></div>
                                </div>
                            </template>
                            <div class="cashup-row">
                                <div>
                                    <button type="button" class="text-sm font-semibold text-primary-700 hover:underline" @click="addCardMachine()">+ Add Card Machine</button>
                                </div>
                            </div>
                            <div class="cashup-row">
                                <div class="font-medium text-gray-900 dark:text-white">Refunds</div>
                                <div><input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $wizardConfig['refundAmount'] }}" x-model.number="refundAmount"></div>
                                <div class="cashup-total text-red-600" x-text="'-' + money(refundAmount || 0)"></div>
                            </div>
                            <div class="cashup-row cashup-foot">
                                <div class="cashup-span-2 text-right font-bold">Card Payments Total</div>
                                <div class="cashup-total font-bold" x-text="money(cardsTotal)"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses --}}
                    <div x-show="step === 3" @if ($step !== 3) x-cloak style="display:none" @endif>
                        <div class="cashup-table overflow-hidden">
                            <div class="cashup-row cashup-head">
                                <div class="cashup-col-label">Description</div>
                                <div class="cashup-col-input">Amount</div>
                                <div class="cashup-col-total">Total Amount</div>
                            </div>
                            @foreach ($expenseRows as $idx => $row)
                                <div class="cashup-row">
                                    <div><input type="text" class="cashup-input cashup-input-wide" placeholder="E.g. Milk" value="{{ $row['description'] }}" x-model="expenses[{{ $idx }}].description"></div>
                                    <div><input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $row['amount'] }}" x-model.number="expenses[{{ $idx }}].amount"></div>
                                    <div class="cashup-total" x-text="money(expenses[{{ $idx }}].amount || 0)">£{{ number_format($row['amount'], 2) }}</div>
                                </div>
                            @endforeach
                            <template x-for="(row, idx) in expenses.slice({{ count($expenseRows) }})" :key="'exp-extra-'+idx">
                                <div class="cashup-row">
                                    <div><input type="text" class="cashup-input cashup-input-wide" placeholder="E.g. Milk" x-model="expenses[{{ count($expenseRows) }} + idx].description"></div>
                                    <div><input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" x-model.number="expenses[{{ count($expenseRows) }} + idx].amount"></div>
                                    <div class="cashup-total" x-text="money(row.amount || 0)"></div>
                                </div>
                            </template>
                            <div class="cashup-row">
                                <div>
                                    <button type="button" class="text-sm font-semibold text-primary-700 hover:underline" @click="expenses.push({ description: '', amount: 0 })">+ Add Expense</button>
                                </div>
                            </div>
                            <div class="cashup-row cashup-foot">
                                <div class="cashup-span-2 text-right font-bold">Expenses Total</div>
                                <div class="cashup-total font-bold" x-text="money(expensesTotal)"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Online --}}
                    <div x-show="step === 4" @if ($step !== 4) x-cloak style="display:none" @endif>
                        <table class="cashup-table">
                            <thead>
                                <tr>
                                    <th class="cashup-col-label">Platform</th>
                                    <th class="cashup-col-input">Amount</th>
                                    <th class="cashup-col-total">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($platformRows as $idx => $row)
                                    <tr>
                                        <td class="font-medium text-gray-900 dark:text-white">{{ $row['platform'] }}</td>
                                        <td>
                                            <input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $row['amount'] }}" x-model.number="platforms[{{ $idx }}].amount">
                                        </td>
                                        <td class="cashup-total" x-text="money(platforms[{{ $idx }}].amount || 0)">£{{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-bold">Online Orders Total</td>
                                    <td class="cashup-total font-bold" x-text="money(onlineTotal)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="cashup-actions mt-6">
                        <div>
                            <button
                                type="button"
                                class="inline-flex rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200"
                                x-show="step > 0"
                                @click="goToStep(step - 1)"
                                @if ($step === 0) style="display:none" @endif
                            >Previous</button>
                        </div>
                        <div class="text-center">
                            <button type="button" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60" @click="saveCashUp()" :disabled="loading">Save Cash Up</button>
                        </div>
                        <div class="text-right">
                            <button
                                type="button"
                                class="inline-flex rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
                                x-show="step < steps.length - 1"
                                @click="goToStep(step + 1)"
                                @if ($step >= 4) style="display:none" @endif
                            >Next Step</button>
                        </div>
                    </div>
                @else
                    <div>
                        <p class="mb-3 text-sm text-gray-500">Enter platform deduction amounts for this shift.</p>
                        <table class="cashup-table">
                            <thead>
                                <tr>
                                    <th class="cashup-col-label">Platform</th>
                                    <th class="cashup-col-input">Amount</th>
                                    <th class="cashup-col-total">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deductionRows as $idx => $row)
                                    <tr>
                                        <td class="font-medium text-gray-900 dark:text-white">{{ $row['platform'] }}</td>
                                        <td>
                                            <input type="number" min="0" step="0.01" class="cashup-input" placeholder="0.00" value="{{ $row['amount'] }}" x-model.number="deductions[{{ $idx }}].amount">
                                        </td>
                                        <td class="cashup-total" x-text="money(deductions[{{ $idx }}].amount || 0)">£{{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-bold">Platform Deductions Total</td>
                                    <td class="cashup-total font-bold" x-text="money(deductionsTotal)"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="cashup-actions mt-6">
                            <div></div>
                            <div class="text-center">
                                <button type="button" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60" @click="saveDeductions()" :disabled="loading">Save Deductions</button>
                            </div>
                            <div></div>
                        </div>
                    </div>
                @endif
            </div>
        </x-admin.card>
    </div>
</x-layouts.staff>
