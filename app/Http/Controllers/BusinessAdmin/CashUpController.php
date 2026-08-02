<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Enums\CashUpShift;
use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\CashUpStoreRequest;
use App\Services\BusinessAdmin\CashUpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class CashUpController extends Controller
{
    public function __construct(private readonly CashUpService $cashUps) {}

    public function index(Request $request): View
    {
        $date = $request->string('date', now()->toDateString())->toString();
        $shift = $request->string(
            'shift',
            now()->hour < 15 ? CashUpShift::Morning->value : CashUpShift::Evening->value,
        )->toString();

        if (! in_array($shift, [CashUpShift::Morning->value, CashUpShift::Evening->value], true)) {
            $shift = CashUpShift::Morning->value;
        }

        $cashUp = $this->cashUps->findOrEmpty($request->user(), $date, $shift);
        $viewTab = $request->input('view', 'cashup');
        if (! in_array($viewTab, ['cashup', 'deductions'], true)) {
            $viewTab = 'cashup';
        }

        return view('business-admin.cash-up.index', [
            'date' => $date,
            'shift' => $shift,
            'viewTab' => $viewTab,
            'cashUp' => $cashUp,
            'coins' => CashUpService::COINS,
            'notes' => CashUpService::NOTES,
            'platforms' => CashUpService::PLATFORMS,
        ]);
    }

    public function store(CashUpStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $cashUp = $this->cashUps->save(
                $request->user(),
                $request->validated(),
                (bool) $request->boolean('overwrite'),
            );
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'code' => 'ALREADY_EXISTS',
                ], 422);
            }
            throw $e;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cash up saved.',
                'cash_up' => $cashUp,
            ]);
        }

        return back()->with('status', 'Cash up saved.');
    }

    public function storeDeductions(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'cashup_date' => ['required', 'date'],
            'shift' => ['required', 'in:Morning,Evening'],
            'deductions' => ['nullable', 'array'],
            'deductions.*.platform' => ['nullable', 'string'],
            'deductions.*.amount' => ['nullable', 'numeric', 'min:0'],
            'overwrite' => ['sometimes', 'boolean'],
        ]);

        try {
            $cashUp = $this->cashUps->saveDeductions(
                $request->user(),
                $data,
                (bool) ($data['overwrite'] ?? false),
            );
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'code' => 'ALREADY_EXISTS',
                ], 422);
            }
            throw $e;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Platform deductions saved.',
                'cash_up' => $cashUp,
            ]);
        }

        return back()->with('status', 'Platform deductions saved.');
    }
}
