<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class StaffStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'pin_code' => 'nullable|string|size:4',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'status' => 'nullable|string|in:active,suspended',
        ];
    }
}
