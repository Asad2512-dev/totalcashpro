<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Organization;
use App\Services\SuperAdmin\BillingExtrasService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DiscountController extends Controller
{
    public function __construct(private readonly BillingExtrasService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Add Discount',
            'active' => 'discounts',
            'action' => route('super-admin.discounts.store'),
            'cancelRoute' => route('super-admin.discounts'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->createDiscount($this->validated($request));

        return redirect()->route('super-admin.discounts')->with('status', 'Discount applied.');
    }

    public function edit(Discount $discount): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Discount',
            'active' => 'discounts',
            'action' => route('super-admin.discounts.update', $discount),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.discounts'),
            'model' => $discount,
            'fields' => $this->fields($discount),
        ]);
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $this->service->updateDiscount($discount, $this->validated($request));

        return redirect()->route('super-admin.discounts')->with('status', 'Discount updated.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $this->service->deleteDiscount($discount);

        return redirect()->route('super-admin.discounts')->with('status', 'Discount removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'type' => ['required', 'in:percentage,custom_price'],
            'grant_type' => ['required', 'in:percentage,custom_price,free,lifetime'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,scheduled,expired'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['grant_type'] === 'free') {
            $data['type'] = 'custom_price';
            $data['custom_price'] = 0;
            $data['value'] = 100;
        }

        if ($data['grant_type'] === 'lifetime') {
            $data['ends_at'] = null;
            $data['notes'] = trim(($data['notes'] ?? '').' Lifetime access');
        }

        return $data;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Discount $discount = null): array
    {
        return [
            ['name' => 'organization_id', 'type' => 'select', 'label' => 'Business', 'value' => $discount?->organization_id, 'options' => Organization::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'grant_type', 'type' => 'select', 'label' => 'Grant type', 'value' => $discount?->grant_type ?? 'percentage', 'options' => [
                'percentage' => 'Percentage discount',
                'custom_price' => 'Custom monthly price',
                'free' => 'Free access (100%)',
                'lifetime' => 'Lifetime access',
            ]],
            ['name' => 'type', 'type' => 'select', 'value' => $discount?->type?->value ?? 'percentage', 'options' => ['percentage' => 'Percentage', 'custom_price' => 'Custom price']],
            ['name' => 'value', 'type' => 'number', 'label' => 'Percentage value', 'value' => $discount?->value],
            ['name' => 'custom_price', 'type' => 'number', 'label' => 'Custom monthly price', 'value' => $discount?->custom_price],
            ['name' => 'status', 'type' => 'select', 'value' => $discount?->status ?? 'active', 'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'scheduled' => 'Scheduled', 'expired' => 'Expired']],
            ['name' => 'starts_at', 'type' => 'date', 'value' => $discount?->starts_at?->format('Y-m-d')],
            ['name' => 'ends_at', 'type' => 'date', 'label' => 'Expiry date', 'value' => $discount?->ends_at?->format('Y-m-d')],
            ['name' => 'notes', 'value' => $discount?->notes, 'full' => true],
        ];
    }
}
