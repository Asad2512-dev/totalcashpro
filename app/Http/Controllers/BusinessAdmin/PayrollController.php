<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Wage;
use App\Services\BusinessAdmin\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payroll,
    ) {}

    public function index(Request $request): View
    {
        $period = $request->input('period', 'current');
        $meta = $this->payroll->formMeta($request->user());

        return view('business-admin.payroll.index', array_merge($meta, [
            'wages' => $this->payroll->list($request->user(), $period),
            'period' => $period,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'hours_worked' => ['required', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->payroll->store($request->user(), $validated);

        return redirect()
            ->route('business-admin.payroll')
            ->with('status', 'Wage record created.');
    }

    public function markPaid(Request $request, Wage $wage): RedirectResponse
    {
        $this->payroll->markPaid($request->user(), (int) $wage->id);

        return redirect()
            ->route('business-admin.payroll')
            ->with('status', 'Wage marked as paid.');
    }
}
