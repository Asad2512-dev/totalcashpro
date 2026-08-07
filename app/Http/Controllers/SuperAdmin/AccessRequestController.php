<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Services\Mail\MailSender;
use App\Services\SuperAdmin\AccessRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AccessRequestController extends Controller
{
    public function __construct(
        private readonly AccessRequestService $service,
        private readonly MailSender $mail,
    ) {}

    public function show(AccessRequest $accessRequest): View
    {
        return view('admin.crud.show', [
            'title' => $accessRequest->business_name,
            'description' => 'Business access request',
            'active' => 'business-requests',
            'backRoute' => route('super-admin.business-requests'),
            'fields' => [
                ['label' => 'Business', 'value' => $accessRequest->business_name],
                ['label' => 'Owner', 'value' => $accessRequest->owner_name],
                ['label' => 'Email', 'value' => $accessRequest->email],
                ['label' => 'Phone', 'value' => $accessRequest->phone],
                ['label' => 'Country', 'value' => $accessRequest->country],
                ['label' => 'Type', 'value' => $accessRequest->business_type],
                ['label' => 'Employees', 'value' => $accessRequest->number_of_employees],
                ['label' => 'Plan', 'value' => $accessRequest->selected_plan instanceof \BackedEnum ? $accessRequest->selected_plan->value : $accessRequest->selected_plan],
                ['label' => 'Status', 'value' => $accessRequest->status instanceof \BackedEnum ? $accessRequest->status->value : $accessRequest->status],
                ['label' => 'Notes', 'value' => $accessRequest->additional_notes ?? '—', 'full' => true],
                ['label' => 'Admin notes', 'value' => $accessRequest->admin_notes ?? '—', 'full' => true],
                ['label' => 'Submitted', 'value' => $accessRequest->created_at?->format('d M Y H:i') ?? '—'],
            ],
            'actions' => view('admin.partials.access-request-actions', ['accessRequest' => $accessRequest]),
        ]);
    }

    public function approve(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $notes = $request->validate(['admin_notes' => ['nullable', 'string']])['admin_notes'] ?? null;
        $result = $this->service->approve($accessRequest, $notes);

        return redirect()
            ->route('super-admin.organizations.show', $result['organization'])
            ->with('status', 'Request approved. Organisation created and owner credentials emailed.')
            ->with('owner_credentials', [
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'password' => $result['password'],
            ]);
    }

    public function reject(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $notes = $request->validate(['admin_notes' => ['nullable', 'string']])['admin_notes'] ?? null;
        $this->service->reject($accessRequest, $notes);

        return redirect()
            ->route('super-admin.business-requests')
            ->with('status', 'Access request rejected.');
    }

    public function email(AccessRequest $accessRequest): RedirectResponse
    {
        $this->mail->sendRaw(
            'We received your TotalCashPro access request for '.$accessRequest->business_name.'. Our team is reviewing it.',
            function ($message) use ($accessRequest): void {
                $message->to($accessRequest->email)->subject('Your TotalCashPro access request');
            },
        );

        return back()->with('status', 'Follow-up email sent to '.$accessRequest->email);
    }
}
