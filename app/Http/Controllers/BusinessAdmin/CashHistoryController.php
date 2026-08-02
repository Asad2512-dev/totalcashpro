<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\CashUpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class CashHistoryController extends Controller
{
    public function __construct(private readonly CashUpService $cashUps) {}

    public function index(Request $request): View
    {
        $period = $request->string('period', 'weekly')->toString();
        $date = $request->input('date', now()->toDateString());
        $history = $this->cashUps->history($request->user(), $period, $date);

        return view('business-admin.cash-history.index', [
            'period' => $history['period'],
            'date' => $date,
            'from' => $history['from'],
            'to' => $history['to'],
            'rows' => $history['rows'],
            'total' => $history['total'],
        ]);
    }
}
