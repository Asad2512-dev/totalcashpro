<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use App\Models\User;
use App\Support\Tenancy\TenantRules;

final class StaffUpdateRequest extends BusinessAdminFormRequest
{
    public function authorize(): bool
    {
        $staff = $this->route('staff');

        return parent::authorize()
            && $staff instanceof User
            && (int) $staff->organization_id === $this->organizationId();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$staffId,
            'phone' => 'nullable|string|max:50',
            'pin_code' => 'nullable|string|size:4|regex:/^\d{4}$/',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'branch_id' => TenantRules::branchId($this->organizationId()),
            'status' => 'nullable|string|in:active,suspended',
        ];
    }
}
