<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\FinanceIncomeEntry;
use App\Models\Spending;
use App\Models\Wage;
use App\Services\BusinessAdmin\Finance\FinanceBankAccountService;
use App\Services\BusinessAdmin\Finance\FinanceBillService;
use App\Services\BusinessAdmin\Finance\FinanceCatalogService;
use App\Services\BusinessAdmin\Finance\FinanceDashboardService;
use App\Services\BusinessAdmin\Finance\FinanceExpenseService;
use App\Services\BusinessAdmin\Finance\FinanceIncomeService;
use App\Services\BusinessAdmin\Finance\FinanceIntegrationService;
use App\Services\BusinessAdmin\Finance\FinancePayrollService;
use App\Services\BusinessAdmin\Finance\FinancePurchaseInvoiceService;
use App\Services\BusinessAdmin\Finance\FinanceReportService;
use App\Services\BusinessAdmin\Finance\FinanceSupplierPaymentService;
use App\Services\BusinessAdmin\ReportCenterService;
use App\Services\BusinessAdmin\ReportExportService;
use App\Support\Reports\ReportCenterFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceDashboardService $dashboard,
        private readonly FinanceCatalogService $catalog,
        private readonly FinanceIncomeService $income,
        private readonly FinanceExpenseService $expenses,
        private readonly FinanceBillService $bills,
        private readonly FinancePurchaseInvoiceService $purchaseInvoices,
        private readonly FinanceSupplierPaymentService $supplierPayments,
        private readonly FinancePayrollService $payroll,
        private readonly FinanceBankAccountService $bankAccounts,
        private readonly FinanceReportService $financeReports,
        private readonly FinanceIntegrationService $integrations,
        private readonly ReportCenterService $reportCenter,
        private readonly ReportExportService $reportExport,
    ) {}

    public function dashboard(Request $request): View
    {
        return view('business-admin.finance.dashboard', [
            'snapshot' => $this->dashboard->snapshot($request->user()),
            'integrations' => $this->integrations->listFor($request->user()),
        ]);
    }

    public function income(Request $request): View
    {
        return view('business-admin.finance.income', array_merge(
            $this->income->formMeta($request->user()),
            [
                'entries' => $this->income->list($request->user(), $request->input('status')),
                'status' => $request->input('status'),
            ],
        ));
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'gross_amount' => ['required_without:net_amount', 'nullable', 'numeric', 'min:0'],
            'net_amount' => ['nullable', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'income_date' => ['required', 'date'],
            'bank_account_id' => ['nullable', 'integer', 'exists:finance_bank_accounts,id'],
            'notes' => ['nullable', 'string'],
            'receipt' => ['nullable', 'file', 'max:5120'],
        ]);

        $this->income->store($request->user(), $validated, $request->file('receipt'));

        return redirect()->route('business-admin.finance.income')->with('status', 'Income entry saved as draft.');
    }

    public function approveIncome(Request $request, FinanceIncomeEntry $entry): RedirectResponse
    {
        $this->income->approve($request->user(), (int) $entry->id);

        return back()->with('status', 'Income approved.');
    }

    public function markIncomePaid(Request $request, FinanceIncomeEntry $entry): RedirectResponse
    {
        $this->income->markPaid($request->user(), (int) $entry->id);

        return back()->with('status', 'Income marked as received.');
    }

    public function expenses(Request $request): View
    {
        return view('business-admin.finance.expenses', array_merge(
            $this->expenses->formMeta($request->user()),
            [
                'expenses' => $this->expenses->list($request->user(), $request->input('status')),
                'status' => $request->input('status'),
                'categories' => $this->catalog->expenseCategories(),
                'paymentMethods' => $this->catalog->paymentMethods(),
            ],
        ));
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:40'],
            'gross_amount' => ['required_without:net_amount', 'nullable', 'numeric', 'min:0'],
            'net_amount' => ['nullable', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'spent_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:40'],
            'bank_account_id' => ['nullable', 'integer', 'exists:finance_bank_accounts,id'],
            'notes' => ['nullable', 'string'],
            'receipt' => ['nullable', 'file', 'max:5120'],
        ]);

        $this->expenses->store($request->user(), $validated, $request->file('receipt'));

        return redirect()->route('business-admin.finance.expenses')->with('status', 'Expense saved as draft.');
    }

    public function approveExpense(Request $request, Spending $expense): RedirectResponse
    {
        $this->expenses->approve($request->user(), (int) $expense->id);

        return back()->with('status', 'Expense approved.');
    }

    public function markExpensePaid(Request $request, Spending $expense): RedirectResponse
    {
        $this->expenses->markPaid($request->user(), (int) $expense->id);

        return back()->with('status', 'Expense marked as paid.');
    }

    public function bills(Request $request): View
    {
        return view('business-admin.finance.bills', array_merge(
            $this->bills->formMeta($request->user()),
            [
                'bills' => $this->bills->list($request->user(), $request->input('status')),
                'status' => $request->input('status'),
                'categories' => $this->catalog->billCategories(),
            ],
        ));
    }

    public function storeBill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:40'],
            'gross_amount' => ['required_without:net_amount', 'nullable', 'numeric', 'min:0'],
            'net_amount' => ['nullable', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'due_date' => ['required', 'date'],
            'bank_account_id' => ['nullable', 'integer', 'exists:finance_bank_accounts,id'],
            'notes' => ['nullable', 'string'],
            'invoice' => ['nullable', 'file', 'max:5120'],
        ]);

        $this->bills->store($request->user(), $validated, $request->file('invoice'));

        return redirect()->route('business-admin.finance.bills')->with('status', 'Bill saved as draft.');
    }

    public function approveBill(Request $request, Bill $bill): RedirectResponse
    {
        $this->bills->approve($request->user(), (int) $bill->id);

        return back()->with('status', 'Bill approved.');
    }

    public function markBillPaid(Request $request, Bill $bill): RedirectResponse
    {
        $this->bills->markPaid($request->user(), (int) $bill->id);

        return back()->with('status', 'Bill marked as paid.');
    }

    public function purchaseInvoices(Request $request): View
    {
        return view('business-admin.finance.purchase-invoices', array_merge(
            $this->purchaseInvoices->formMeta($request->user()),
            [
                'invoices' => $this->purchaseInvoices->list($request->user(), $request->input('status')),
                'status' => $request->input('status'),
            ],
        ));
    }

    public function storePurchaseInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'invoice_no' => ['required', 'string', 'max:80'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'invoice' => ['nullable', 'file', 'max:5120'],
        ]);

        $this->purchaseInvoices->store($request->user(), $validated, $request->file('invoice'));

        return redirect()->route('business-admin.finance.purchase-invoices')->with('status', 'Purchase invoice recorded.');
    }

    public function supplierPayments(Request $request): View
    {
        return view('business-admin.finance.supplier-payments', array_merge(
            $this->supplierPayments->formMeta($request->user()),
            [
                'payments' => $this->supplierPayments->list($request->user()),
            ],
        ));
    }

    public function storeSupplierPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_invoice_id' => ['required', 'integer', 'exists:supplier_invoices,id'],
            'payment_date' => ['required', 'date'],
            'bank_account_id' => ['nullable', 'integer', 'exists:finance_bank_accounts,id'],
            'reference' => ['nullable', 'string', 'max:120'],
            'receipt' => ['nullable', 'file', 'max:5120'],
        ]);

        $this->supplierPayments->store($request->user(), $validated, $request->file('receipt'));

        return redirect()->route('business-admin.finance.supplier-payments')->with('status', 'Supplier payment recorded.');
    }

    public function payroll(Request $request): View
    {
        $period = $request->input('period', 'current');

        return view('business-admin.finance.payroll', array_merge(
            $this->payroll->formMeta($request->user()),
            [
                'wages' => $this->payroll->list($request->user(), $period),
                'period' => $period,
            ],
        ));
    }

    public function storePayroll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'hours_worked' => ['required', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->payroll->storeManual($request->user(), $validated);

        return redirect()->route('business-admin.finance.payroll')->with('status', 'Wage draft created.');
    }

    public function generatePayroll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'week_start' => ['nullable', 'date'],
            'payment_due_date' => ['nullable', 'date', 'after_or_equal:week_start'],
            'notes' => ['nullable', 'string'],
        ]);

        $run = $this->payroll->generateFromAttendance($request->user(), $validated);

        return redirect()
            ->route('business-admin.finance.payroll', ['period' => 'draft'])
            ->with('status', "Payroll run generated for week starting {$run->week_start->format('d M Y')}.");
    }

    public function approvePayrollRun(Request $request, int $run): RedirectResponse
    {
        $this->payroll->approveRun($request->user(), $run);

        return back()->with('status', 'Payroll run approved.');
    }

    public function approveWage(Request $request, Wage $wage): RedirectResponse
    {
        $this->payroll->approveWage($request->user(), (int) $wage->id);

        return back()->with('status', 'Wage approved.');
    }

    public function markWagePaid(Request $request, Wage $wage): RedirectResponse
    {
        $this->payroll->markPaid($request->user(), (int) $wage->id);

        return back()->with('status', 'Wage marked as paid.');
    }

    public function weeklyWages(Request $request): View
    {
        $data = $this->payroll->weeklyWages($request->user(), $request->input('week'));

        return view('business-admin.finance.weekly-wages', $data);
    }

    public function bankAccounts(Request $request): View
    {
        return view('business-admin.finance.bank-accounts', [
            'accounts' => $this->bankAccounts->list($request->user()),
        ]);
    }

    public function storeBankAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'sort_code' => ['nullable', 'string', 'max:16'],
            'account_number_last4' => ['nullable', 'string', 'max:4'],
            'opening_balance' => ['nullable', 'numeric'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $this->bankAccounts->store($request->user(), $validated);

        return redirect()->route('business-admin.finance.bank-accounts')->with('status', 'Bank account added.');
    }

    public function cashFlow(Request $request): View
    {
        return view('business-admin.finance.cash-flow', [
            'report' => $this->financeReports->cashFlow($request->user()),
        ]);
    }

    public function profitLoss(Request $request): View
    {
        return view('business-admin.finance.profit-loss', [
            'report' => $this->financeReports->profitAndLoss($request->user()),
        ]);
    }

    public function vatSummary(Request $request): View
    {
        return view('business-admin.finance.vat', [
            'report' => $this->financeReports->vatSummary($request->user()),
        ]);
    }

    public function reports(Request $request): View
    {
        $filter = ReportCenterFilter::fromRequest($request);

        return view('business-admin.finance.reports', [
            'report' => $this->reportCenter->build($request->user(), $filter),
            'filter' => $filter,
        ]);
    }

    public function exportReports(Request $request): StreamedResponse
    {
        $filter = ReportCenterFilter::fromRequest($request);
        $report = $this->reportCenter->build($request->user(), $filter);
        $format = $request->string('format', 'csv')->toString();
        $slug = $filter->reportType->value;
        $filename = "finance-{$slug}-{$filter->from}-to-{$filter->to}";

        return match ($format) {
            'excel', 'xls' => $this->reportExport->excel($report['table'], "{$filename}.xls"),
            default => $this->reportExport->csv($report['table'], "{$filename}.csv"),
        };
    }
}
