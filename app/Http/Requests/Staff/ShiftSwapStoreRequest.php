<?php

declare(strict_types=1);

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

final class ShiftSwapStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rota_shift_id' => ['required', 'integer', 'exists:rota_shifts,id'],
            'target_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
