<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('staff.profile.edit', [
            'user' => $request->user()->loadMissing('branch'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()
            ->route('staff.profile')
            ->with('status', 'Profile updated successfully.');
    }
}
