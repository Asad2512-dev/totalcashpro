<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\CashDrawer;
use App\Models\PettyCashAccount;
use App\Services\BusinessAdmin\CashDrawerService;
use App\Services\BusinessAdmin\PettyCashService;
use App\Services\BusinessAdmin\RecurringBillService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FinanceToolsController extends Controller
{
    public function __construct(
        private readonly RecurringBillService $recurringBills,
        private readonly PettyCashService $pettyCash,
        private readonly CashDrawerService $cashDrawers,
    ) {}

    public function recurringBills(Request $request): View
    {
        return view('business-admin.finance.recurring-bills', [
            'templates' => $this->recurringBills->list($request->user()),
        ]);
    }

    public function storeRecurringBill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'frequency' => ['required', 'in:weekly,monthly,quarterly,yearly'],
            'next_due_date' => ['required', 'date'],
        ]);

        $this->recurringBills->store($request->user(), $validated);

        return back()->with('status', 'Recurring bill template saved.');
    }

    public function pettyCash(Request $request): View
    {
        return view('business-admin.finance.petty-cash', [
            'accounts' => $this->pettyCash->listAccounts($request->user()),
        ]);
    }

    public function storePettyCashAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'float_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->pettyCash->storeAccount($request->user(), $validated);

        return back()->with('status', 'Petty cash float created.');
    }

    public function storePettyCashTransaction(Request $request, PettyCashAccount $account): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:top_up,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'transaction_date' => ['nullable', 'date'],
        ]);

        $this->pettyCash->recordTransaction($request->user(), $account, $validated);

        return back()->with('status', 'Petty cash transaction recorded.');
    }

    public function cashDrawers(Request $request): View
    {
        return view('business-admin.finance.cash-drawers', [
            'drawers' => $this->cashDrawers->list($request->user()),
        ]);
    }

    public function updateCashDrawer(Request $request, CashDrawer $drawer): RedirectResponse
    {
        $validated = $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ]);

        $this->cashDrawers->updateOpeningBalance(
            $request->user(),
            $drawer,
            (float) $validated['opening_balance'],
        );

        return back()->with('status', 'Cash drawer balance updated.');
    }
}
