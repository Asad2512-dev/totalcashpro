<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\SuperAdmin\SupportTicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SupportTicketController extends Controller
{
    public function __construct(private readonly SupportTicketService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'New Ticket',
            'active' => 'support',
            'action' => route('super-admin.support.store'),
            'cancelRoute' => route('super-admin.support'),
            'fields' => [
                ['name' => 'organization_id', 'type' => 'select', 'label' => 'Business', 'options' => ['' => '—'] + Organization::query()->orderBy('name')->pluck('name', 'id')->all()],
                ['name' => 'user_id', 'type' => 'select', 'label' => 'Assign to', 'options' => ['' => '—'] + User::query()->orderBy('name')->pluck('name', 'id')->all()],
                ['name' => 'subject', 'full' => true],
                ['name' => 'body', 'type' => 'textarea', 'full' => true],
                ['name' => 'priority', 'type' => 'select', 'value' => 'normal', 'options' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent']],
                ['name' => 'status', 'type' => 'select', 'value' => 'open', 'options' => ['open' => 'Open', 'pending' => 'Pending', 'closed' => 'Closed']],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'status' => ['required', 'in:open,pending,closed'],
        ]);

        $ticket = $this->service->create($data);

        return redirect()->route('super-admin.support.show', $ticket)->with('status', 'Ticket created.');
    }

    public function show(SupportTicket $support): View
    {
        $support->load(['organization', 'user', 'replies.user']);

        return view('admin.support.show', [
            'ticket' => $support,
        ]);
    }

    public function update(Request $request, SupportTicket $support): RedirectResponse
    {
        $data = $request->validate([
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'status' => ['required', 'in:open,pending,closed'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $this->service->update($support, $data);

        return back()->with('status', 'Ticket updated.');
    }

    public function reply(Request $request, SupportTicket $support): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $this->service->reply($support, $data['body'], $request->boolean('is_internal'));

        return back()->with('status', 'Reply added.');
    }

    public function close(SupportTicket $support): RedirectResponse
    {
        $this->service->close($support);

        return back()->with('status', 'Ticket closed.');
    }
}
