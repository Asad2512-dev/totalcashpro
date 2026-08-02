<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ContactMessageController extends Controller
{
    public function show(ContactMessage $contactMessage): View
    {
        return view('admin.crud.show', [
            'title' => $contactMessage->subject,
            'active' => 'contact-messages',
            'backRoute' => route('super-admin.contact-messages'),
            'fields' => [
                ['label' => 'Name', 'value' => $contactMessage->name],
                ['label' => 'Email', 'value' => $contactMessage->email],
                ['label' => 'Phone', 'value' => $contactMessage->phone ?? '—'],
                ['label' => 'Subject', 'value' => $contactMessage->subject],
                ['label' => 'Message', 'value' => $contactMessage->message, 'full' => true],
                ['label' => 'Received', 'value' => $contactMessage->created_at?->format('d M Y H:i') ?? '—'],
            ],
        ]);
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('super-admin.contact-messages')->with('status', 'Message deleted.');
    }
}
