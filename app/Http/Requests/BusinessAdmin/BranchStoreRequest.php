<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class BranchStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'receipt_footer' => ['nullable', 'string'],
            'manager_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'finance_bank_account_id' => ['nullable', 'integer', 'exists:finance_bank_accounts,id'],
            'drawer_opening_balance' => ['nullable', 'numeric', 'min:0'],
            'opening_hours' => ['nullable', 'array'],
        ];
    }
}
