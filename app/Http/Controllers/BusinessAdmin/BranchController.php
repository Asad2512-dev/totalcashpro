<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class BranchController extends Controller
{
    public function __construct(private readonly BranchContext $branchContext) {}

    public function select(Request $request): RedirectResponse
    {
        $value = $request->input('branch_id');
        $this->branchContext->setBranchId(
            ($value === null || $value === '' || $value === 'all') ? null : (int) $value,
            $request->user(),
        );

        return back()->with('status', 'Branch filter updated.');
    }
}
