<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function edit(Request $request): View
    {
        $user = $request->user();
        $organization = $user->organization;

        return view('business-admin.settings.edit', [
            'organization' => $organization,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string|max:100',
            'vat_number' => 'nullable|string|max:50',
            'company_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'settings' => 'nullable|array',
        ]);

        $this->settings->update($user, $validated);

        return redirect()
            ->route('business-admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
