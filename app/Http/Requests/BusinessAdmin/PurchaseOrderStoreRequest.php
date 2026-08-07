<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class PurchaseOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'expected_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.inventory_item_id' => ['nullable', 'integer', 'exists:inventory_items,id'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'lines.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'lines.*.vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
