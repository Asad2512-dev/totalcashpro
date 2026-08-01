<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Actions\Marketing\StoreContactMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreContactMessageRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ContactController extends Controller
{
    public function create(): View
    {
        return view('marketing.contact', [
            'seo' => [
                'title' => 'Contact TotalCashPro',
                'description' => 'Contact the TotalCashPro team with questions about Basic or Professional plans, demos, or account access.',
            ],
        ]);
    }

    public function store(
        StoreContactMessageRequest $request,
        StoreContactMessageAction $action,
    ): RedirectResponse {
        $action->execute($request->validated());

        return redirect()
            ->route('contact')
            ->with('status', 'Thanks — your message has been sent. We will reply within 24 hours.');
    }
}
