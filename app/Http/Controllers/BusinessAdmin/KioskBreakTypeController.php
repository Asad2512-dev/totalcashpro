<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\KioskBreakType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class KioskBreakTypeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $organization = $request->user()->organization;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_paid' => ['nullable', 'boolean'],
            'max_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        KioskBreakType::query()->create([
            'organization_id' => $organization->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_paid' => (bool) ($data['is_paid'] ?? false),
            'max_duration_minutes' => $data['max_duration_minutes'] ?? null,
            'is_active' => true,
            'display_order' => $data['display_order'] ?? 0,
        ]);

        return back()->with('status', 'Break type created.');
    }

    public function update(Request $request, KioskBreakType $breakType): RedirectResponse
    {
        $this->authorizeBreakType($request, $breakType);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_paid' => ['nullable', 'boolean'],
            'max_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $breakType->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_paid' => (bool) ($data['is_paid'] ?? false),
            'max_duration_minutes' => $data['max_duration_minutes'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'display_order' => $data['display_order'] ?? $breakType->display_order,
        ]);

        return back()->with('status', 'Break type updated.');
    }

    public function destroy(Request $request, KioskBreakType $breakType): RedirectResponse
    {
        $this->authorizeBreakType($request, $breakType);
        $breakType->update(['is_active' => false]);

        return back()->with('status', 'Break type deactivated.');
    }

    private function authorizeBreakType(Request $request, KioskBreakType $breakType): void
    {
        if ((int) $breakType->organization_id !== (int) $request->user()->organization_id) {
            abort(403);
        }
    }
}
