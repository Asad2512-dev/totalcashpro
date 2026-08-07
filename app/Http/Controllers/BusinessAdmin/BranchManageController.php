<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\BranchStoreRequest;
use App\Http\Requests\BusinessAdmin\BranchUpdateRequest;
use App\Models\Branch;
use App\Services\BusinessAdmin\BranchManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BranchManageController extends Controller
{
    public function __construct(
        private readonly BranchManagementService $branches,
    ) {}

    public function index(Request $request): View
    {
        return view('business-admin.branches.index', [
            'branches' => $this->branches->list($request->user()),
            'managers' => $this->branches->managerOptions($request->user()),
            'bankAccounts' => $this->branches->bankAccountOptions($request->user()),
        ]);
    }

    public function store(BranchStoreRequest $request): RedirectResponse
    {
        $this->branches->create($request->user(), $request->validated());

        return redirect()
            ->route('business-admin.branches')
            ->with('status', 'Branch created.');
    }

    public function update(BranchUpdateRequest $request, Branch $branch): RedirectResponse
    {
        $this->branches->update($request->user(), $branch, $request->validated());

        return redirect()
            ->route('business-admin.branches')
            ->with('status', 'Branch updated.');
    }
}
