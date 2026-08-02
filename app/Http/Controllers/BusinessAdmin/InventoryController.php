<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request): View
    {
        return view(
            'business-admin.inventory.index',
            $this->inventory->list($request->user(), $request->input('q')),
        );
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'branch_id' => ['required', 'integer'],
        ]);

        $this->inventory->storeCategory($request->user(), $data);

        return back()->with('status', 'Category created.');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'branch_id' => ['required', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'packaging' => ['required', 'in:box,pcs,box.pcs'],
            'pcs_per_box' => ['required', 'integer', 'min:1'],
            'stock_total_pcs' => ['required', 'integer', 'min:0'],
            'stock_limit' => ['required', 'integer', 'min:0'],
        ]);

        $this->inventory->storeItem($request->user(), $data);

        return back()->with('status', 'Product added.');
    }

    public function storeCount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer'],
            'new_pcs' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->inventory->recordCount($request->user(), $data);

        return back()->with('status', 'Stock count saved.');
    }
}
