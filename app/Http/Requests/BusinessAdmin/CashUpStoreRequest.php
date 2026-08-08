<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class CashUpStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cashup_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:Morning,Evening'],
            'cash_drawer_id' => ['nullable', 'integer'],
            'opening_float' => ['nullable', 'numeric', 'min:0'],
            'opening_float_count' => ['nullable', 'array'],
            'opening_float_count.*.label' => ['nullable', 'string'],
            'opening_float_count.*.qty' => ['nullable', 'integer', 'min:0'],
            'opening_float_count.*.value' => ['nullable', 'numeric', 'min:0'],
            'float_adjustment_reason' => ['nullable', 'string', 'max:500'],
            'cash_sales_total' => ['nullable', 'numeric', 'min:0'],
            'variance_reason' => ['nullable', 'string', 'max:500'],
            'submit' => ['sometimes', 'boolean'],
            'overwrite' => ['sometimes', 'boolean'],
            'coins' => ['nullable', 'array'],
            'coins.*.coin' => ['nullable', 'string'],
            'coins.*.qty' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'array'],
            'notes.*.note' => ['nullable', 'string'],
            'notes.*.qty' => ['nullable', 'numeric', 'min:0'],
            'notes.*.amount' => ['nullable', 'numeric', 'min:0'],
            'notes.*.is_qty' => ['nullable', 'boolean'],
            'cards' => ['nullable', 'array'],
            'cards.*.payment_type' => ['nullable', 'string'],
            'cards.*.type' => ['nullable', 'string', 'in:machine,refund'],
            'cards.*.amount' => ['nullable', 'numeric', 'min:0'],
            'expenses' => ['nullable', 'array'],
            'expenses.*.description' => ['nullable', 'string'],
            'expenses.*.amount' => ['nullable', 'numeric', 'min:0'],
            'online' => ['nullable', 'array'],
            'online.*.platform' => ['nullable', 'string'],
            'online.*.amount' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'array'],
            'deductions.*.platform' => ['nullable', 'string'],
            'deductions.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
