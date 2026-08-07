<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Services\BusinessAdmin\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountingController extends Controller
{
    public function __construct(
        private readonly AccountingService $accounting,
    ) {}

    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'overview');
        if (! in_array($tab, ['overview', 'bills', 'spendings'], true)) {
            $tab = 'overview';
        }

        $user = $request->user();

        return view('business-admin.accounting.index', [
            'tab' => $tab,
            'overview' => $this->accounting->overview($user),
            'bills' => $tab === 'bills' ? $this->accounting->listBills($user) : collect(),
            'spendings' => $tab === 'spendings' ? $this->accounting->listSpendings($user) : collect(),
            'billCategories' => $this->accounting->billCategories(),
            'spendingCategories' => $this->accounting->spendingCategories(),
            'paymentMethods' => $this->accounting->paymentMethods(),
        ]);
    }

    public function storeBill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:40'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->accounting->storeBill($request->user(), $validated);

        return redirect()
            ->route('business-admin.accounting', ['tab' => 'bills'])
            ->with('status', 'Bill added.');
    }

    public function storeSpending(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:40'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->accounting->storeSpending($request->user(), $validated);

        return redirect()
            ->route('business-admin.accounting', ['tab' => 'spendings'])
            ->with('status', 'Spending recorded.');
    }

    public function markBillPaid(Request $request, Bill $bill): RedirectResponse
    {
        $this->accounting->markBillPaid($request->user(), (int) $bill->id);

        return redirect()
            ->route('business-admin.accounting', ['tab' => 'bills'])
            ->with('status', 'Bill marked as paid.');
    }
}
