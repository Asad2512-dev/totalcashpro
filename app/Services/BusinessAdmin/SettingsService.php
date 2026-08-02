<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Organization;
use App\Models\User;

final class SettingsService implements ServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): Organization
    {
        $organization = $user->organization;

        if ($organization === null) {
            abort(403, 'User does not belong to an organization.');
        }

        $organization->update([
            'name' => $data['name'],
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'vat_number' => $data['vat_number'] ?? null,
            'company_number' => $data['company_number'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'settings' => array_merge($organization->settings ?? [], $data['settings'] ?? []),
        ]);

        return $organization->refresh();
    }
}
