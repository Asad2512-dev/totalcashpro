<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\CrmCustomerStoreRequest;
use App\Models\CrmCustomer;
use App\Services\BusinessAdmin\CrmCustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CrmController extends Controller
{
    public function __construct(private readonly CrmCustomerService $crm) {}

    public function index(Request $request): View
    {
        return view('business-admin.crm.index', [
            'customers' => $this->crm->list(
                $request->user(),
                $request->input('q'),
                $request->input('marketing'),
            ),
            'search' => $request->input('q'),
            'marketing' => $request->input('marketing'),
        ]);
    }

    public function show(Request $request, CrmCustomer $crmCustomer): View
    {
        $this->authorize('view', $crmCustomer);

        return view('business-admin.crm.show', [
            'customer' => $this->crm->findForAdmin($request->user(), $crmCustomer),
            'timeline' => $this->crm->timeline($request->user(), $crmCustomer),
        ]);
    }

    public function store(CrmCustomerStoreRequest $request): RedirectResponse
    {
        $this->crm->store($request->user(), $request->validated());

        return back()->with('status', 'Customer added.');
    }

    public function update(Request $request, CrmCustomer $crmCustomer): RedirectResponse
    {
        $this->authorize('update', $crmCustomer);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'marketing_email' => ['nullable', 'boolean'],
            'marketing_sms' => ['nullable', 'boolean'],
        ]);

        $this->crm->update($request->user(), $crmCustomer, [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'marketing_preferences' => [
                'email' => $request->boolean('marketing_email'),
                'sms' => $request->boolean('marketing_sms'),
            ],
        ]);

        return back()->with('status', 'Customer updated.');
    }

    public function destroy(Request $request, CrmCustomer $crmCustomer): RedirectResponse
    {
        $this->authorize('update', $crmCustomer);
        $this->crm->delete($request->user(), $crmCustomer);

        return redirect()->route('business-admin.crm')->with('status', 'Customer removed.');
    }

    public function storeNote(Request $request, CrmCustomer $crmCustomer): RedirectResponse
    {
        $this->authorize('update', $crmCustomer);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $this->crm->addNote($request->user(), $crmCustomer, $validated['body']);

        return back()->with('status', 'Note added.');
    }

    public function storeVisit(Request $request, CrmCustomer $crmCustomer): RedirectResponse
    {
        $this->authorize('update', $crmCustomer);

        $validated = $request->validate([
            'visited_at' => ['nullable', 'date'],
            'spend_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->crm->addVisit($request->user(), $crmCustomer, $validated);

        return back()->with('status', 'Visit recorded.');
    }
}
