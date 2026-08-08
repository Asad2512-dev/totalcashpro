<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\InventoryStocktakeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StocktakeController extends Controller
{
    public function __construct(private readonly InventoryStocktakeService $stocktakes) {}

    public function index(Request $request): View
    {
        $stocktake = $this->stocktakes->currentOrCreate($request->user());

        return view('staff.stocktake.index', [
            'stocktake' => $stocktake,
        ]);
    }

    public function save(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'stocktake_id' => ['required', 'integer'],
            'items' => ['required', 'array'],
            'items.*.inventory_item_id' => ['required', 'integer'],
            'items.*.counted_qty' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $stocktake = \App\Models\InventoryStocktake::query()
            ->where('organization_id', $request->user()->organization_id)
            ->findOrFail((int) $validated['stocktake_id']);

        $stocktake = $this->stocktakes->saveCounts($request->user(), $stocktake, $validated['items']);

        if ($request->expectsJson()) {
            return response()->json(['stocktake' => $stocktake]);
        }

        return back()->with('status', 'Counts saved.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate(['stocktake_id' => ['required', 'integer']]);

        $stocktake = \App\Models\InventoryStocktake::query()
            ->where('organization_id', $request->user()->organization_id)
            ->findOrFail((int) $validated['stocktake_id']);

        $this->stocktakes->submit($request->user(), $stocktake);

        return redirect()->route('staff.stocktake')->with('status', 'Weekly stocktake submitted.');
    }
}
