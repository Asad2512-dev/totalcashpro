<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class BranchManageController extends Controller
{
    public function __construct(
        private readonly BranchContext $branchContext,
    ) {}

    public function index(Request $request): View
    {
        return view('business-admin.branches.index', [
            'branches' => $this->branchContext->resolveBranches($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
        ]);

        Branch::query()->create([
            'organization_id' => $request->user()->organization_id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(6)),
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'open',
        ]);

        return redirect()
            ->route('business-admin.branches')
            ->with('status', 'Branch created.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        if ((int) $branch->organization_id !== (int) $request->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
        ]);

        $branch->update($validated);

        return redirect()
            ->route('business-admin.branches')
            ->with('status', 'Branch updated.');
    }
}
