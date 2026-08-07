<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Illuminate\Validation\Rule;

final class TenantRules
{
    public static function branchId(?int $organizationId, bool $required = false): array
    {
        $rule = Rule::exists('branches', 'id')
            ->where(fn ($query) => $query->where('organization_id', $organizationId)->whereNull('deleted_at'));

        return $required ? ['required', 'integer', $rule] : ['nullable', 'integer', $rule];
    }

    public static function supplierId(?int $organizationId, bool $required = true): array
    {
        $rule = Rule::exists('suppliers', 'id')
            ->where(fn ($query) => $query->where('organization_id', $organizationId));

        return $required ? ['required', 'integer', $rule] : ['nullable', 'integer', $rule];
    }

    public static function inventoryItemId(?int $organizationId, bool $required = false): array
    {
        $rule = Rule::exists('inventory_items', 'id')
            ->where(fn ($query) => $query->where('organization_id', $organizationId));

        return $required ? ['required', 'integer', $rule] : ['nullable', 'integer', $rule];
    }

    public static function staffUserId(?int $organizationId, bool $required = false): array
    {
        $rule = Rule::exists('users', 'id')
            ->where(fn ($query) => $query->where('organization_id', $organizationId));

        return $required ? ['required', 'integer', $rule] : ['nullable', 'integer', $rule];
    }

    public static function financeBankAccountId(?int $organizationId, bool $required = false): array
    {
        $rule = Rule::exists('finance_bank_accounts', 'id')
            ->where(fn ($query) => $query->where('organization_id', $organizationId));

        return $required ? ['required', 'integer', $rule] : ['nullable', 'integer', $rule];
    }
}
