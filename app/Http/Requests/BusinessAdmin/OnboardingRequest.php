<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use Illuminate\Foundation\Http\FormRequest;

final class OnboardingRequest extends FormRequest
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
            'step' => ['required', 'integer', 'min:1', 'max:8'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'size:3'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'drawer_opening_balance' => ['nullable', 'numeric', 'min:0'],
            'staff_invites' => ['nullable', 'string', 'max:2000'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_contact' => ['nullable', 'string', 'max:255'],
            'supplier_email' => ['nullable', 'email', 'max:255'],
            'supplier_phone' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function businessPayload(): array
    {
        return [
            'business_name' => $this->input('business_name'),
            'phone' => $this->input('phone'),
            'tax_number' => $this->input('tax_number'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function branchPayload(): array
    {
        return [
            'name' => $this->input('branch_name'),
            'city' => $this->input('city'),
            'address' => $this->input('address'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function settingsPayload(): array
    {
        return [
            'currency' => $this->input('currency'),
            'timezone' => $this->input('timezone'),
            'vat_rate' => $this->input('vat_rate'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function supplierPayload(): array
    {
        return [
            'supplier_name' => $this->input('supplier_name'),
            'supplier_contact' => $this->input('supplier_contact'),
            'supplier_email' => $this->input('supplier_email'),
            'supplier_phone' => $this->input('supplier_phone'),
        ];
    }

    /**
     * @return list<string>
     */
    public function staffInviteEmails(): array
    {
        $raw = (string) $this->input('staff_invites', '');

        if ($raw === '') {
            return [];
        }

        return preg_split('/[\s,;]+/', $raw) ?: [];
    }
}
