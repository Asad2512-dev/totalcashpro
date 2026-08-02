<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Http\Requests\BaseFormRequest;

final class StoreOrganizationRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'size:2'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'status' => ['required', 'in:pending,active,trial,suspended,cancelled'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'owner_name.required' => 'Enter the business owner name. Exactly one owner account will be created.',
            'owner_email.required' => 'Enter the owner email. Login credentials will be sent here.',
            'owner_email.unique' => 'This email is already registered. Use a different owner email.',
        ];
    }
}
