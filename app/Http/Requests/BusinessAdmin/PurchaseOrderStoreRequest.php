<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use App\Support\Tenancy\TenantRules;

final class PurchaseOrderStoreRequest extends BusinessAdminFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $organizationId = $this->organizationId();

        return [
            'supplier_id' => TenantRules::supplierId($organizationId),
            'expected_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.inventory_item_id' => TenantRules::inventoryItemId($organizationId),
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'lines.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'lines.*.vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
