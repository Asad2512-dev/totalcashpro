<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Organization;
use App\Models\Plan;
use App\Services\SuperAdmin\BillingExtrasService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class CouponController extends Controller
{
    public function __construct(private readonly BillingExtrasService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Coupon',
            'active' => 'coupons',
            'action' => route('super-admin.coupons.store'),
            'cancelRoute' => route('super-admin.coupons'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->createCoupon($this->validated($request));

        return redirect()->route('super-admin.coupons')->with('status', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Coupon',
            'active' => 'coupons',
            'action' => route('super-admin.coupons.update', $coupon),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.coupons'),
            'model' => $coupon,
            'fields' => $this->fields($coupon),
        ]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $this->service->updateCoupon($coupon, $this->validated($request));

        return redirect()->route('super-admin.coupons')->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->service->deleteCoupon($coupon);

        return redirect()->route('super-admin.coupons')->with('status', 'Coupon deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:active,inactive,expired'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Coupon $coupon = null): array
    {
        return [
            ['name' => 'code', 'value' => $coupon?->code],
            ['name' => 'type', 'type' => 'select', 'value' => $coupon?->type?->value ?? 'percentage', 'options' => ['percentage' => 'Percentage', 'fixed' => 'Fixed amount']],
            ['name' => 'value', 'type' => 'number', 'value' => $coupon?->value ?? 10],
            ['name' => 'max_uses', 'type' => 'number', 'label' => 'Usage limit', 'value' => $coupon?->max_uses],
            ['name' => 'starts_at', 'type' => 'date', 'value' => $coupon?->starts_at?->format('Y-m-d')],
            ['name' => 'expires_at', 'type' => 'date', 'value' => $coupon?->expires_at?->format('Y-m-d')],
            ['name' => 'status', 'type' => 'select', 'value' => $coupon?->status ?? 'active', 'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'expired' => 'Expired']],
            ['name' => 'plan_id', 'type' => 'select', 'label' => 'Specific plan', 'value' => $coupon?->plan_id, 'options' => ['' => 'Any plan'] + Plan::query()->pluck('name', 'id')->all()],
            ['name' => 'organization_id', 'type' => 'select', 'label' => 'Specific organisation', 'value' => $coupon?->organization_id, 'options' => ['' => 'Any organisation'] + Organization::query()->pluck('name', 'id')->all()],
        ];
    }
}
