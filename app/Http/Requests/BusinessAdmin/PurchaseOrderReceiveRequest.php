<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class PurchaseOrderReceiveRequest extends FormRequest
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
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.purchase_order_line_id' => ['required', 'integer', 'exists:purchase_order_lines,id'],
            'lines.*.quantity_received' => ['required', 'numeric', 'min:0'],
            'lines.*.quantity_damaged' => ['nullable', 'numeric', 'min:0'],
            'lines.*.quantity_missing' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
