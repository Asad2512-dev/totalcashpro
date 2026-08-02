<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class InventoryHistoryController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request): View
    {
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to = $request->input('to', now()->toDateString());
        $history = $this->inventory->history($request->user(), $from, $to);

        return view('business-admin.inventory.history', [
            'counts' => $history['counts'],
            'from' => $from,
            'to' => $to,
        ]);
    }
}
