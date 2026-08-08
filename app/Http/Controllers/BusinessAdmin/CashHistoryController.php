<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\CashDrawerService;
use App\Services\BusinessAdmin\CashUpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class CashHistoryController extends Controller
{
    public function __construct(
        private readonly CashUpService $cashUps,
        private readonly CashDrawerService $drawers,
    ) {}

    public function index(Request $request): View
    {
        $period = $request->string('period', 'weekly')->toString();
        $date = $request->input('date', now()->toDateString());
        $drawerId = $request->integer('cash_drawer_id') ?: null;
        $status = $request->input('status');

        $history = $this->cashUps->history(
            $request->user(),
            $period,
            $date,
            $drawerId,
            $status,
        );

        return view('business-admin.cash-history.index', [
            'period' => $history['period'],
            'date' => $date,
            'from' => $history['from'],
            'to' => $history['to'],
            'rows' => $history['rows'],
            'total' => $history['total'],
            'drawers' => $this->drawers->list($request->user(), withOpenSession: false),
            'selectedDrawerId' => $drawerId,
            'selectedStatus' => $status,
        ]);
    }

    public function show(Request $request, int $cashUp): View
    {
        $record = \App\Models\CashUp::query()
            ->with(['branch', 'cashDrawer', 'creator', 'approver'])
            ->where('organization_id', $request->user()->organization_id)
            ->findOrFail($cashUp);

        return view('business-admin.cash-history.show', [
            'cashUp' => $record,
        ]);
    }
}
