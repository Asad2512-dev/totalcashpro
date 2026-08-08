<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\BreakType;
use App\Enums\RotaEnforcementMode;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\Organization;
use App\Models\OrganizationKioskSetting;

final class KioskConfigurationService implements ServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        $breakTypes = [];
        foreach (BreakType::cases() as $type) {
            $breakTypes[$type->value] = $type->defaultConfig();
        }

        return [
            'allow_clock_in' => true,
            'allow_clock_out' => true,
            'allow_breaks' => true,
            'rota_enforcement' => RotaEnforcementMode::Disabled->value,
            'early_clock_in_minutes' => 15,
            'late_clock_in_minutes' => 15,
            'offline_mode_enabled' => false,
            'require_admin_authentication' => true,
            'break_types' => $breakTypes,
        ];
    }

    /**
     * Resolved settings: organization → branch → kiosk.
     *
     * @return array<string, mixed>
     */
    public function forKiosk(BranchKiosk $kiosk): array
    {
        $defaults = $this->defaults();
        $organization = $kiosk->organization ?? Organization::query()->find($kiosk->organization_id);
        $branch = $kiosk->branch ?? Branch::query()->find($kiosk->branch_id);

        $orgSettings = (array) data_get($organization?->settings, 'kiosk', []);
        $branchSettings = (array) data_get($branch?->settings ?? [], 'kiosk', []);
        $kioskSettings = (array) ($kiosk->settings ?? []);

        return $this->mergeSettings($defaults, $orgSettings, $branchSettings, $kioskSettings);
    }

    /**
     * @return array<string, mixed>
     */
    public function forContext(int $organizationId, int $branchId, ?OrganizationKioskSetting $kioskSettings = null): array
    {
        $defaults = $this->defaults();
        $organization = Organization::query()->find($organizationId);
        $branch = Branch::query()->find($branchId);

        $orgSettings = (array) data_get($organization?->settings, 'kiosk', []);
        $branchSettings = (array) data_get($branch?->settings ?? [], 'kiosk', []);
        $orgKioskSettings = (array) ($kioskSettings?->settings ?? []);

        $merged = $this->mergeSettings($defaults, $orgSettings, $branchSettings, $orgKioskSettings);
        $merged['display_name'] = $kioskSettings?->display_name ?? $merged['display_name'] ?? 'Staff Clock';
        $merged['show_attendance_list'] = $kioskSettings?->show_attendance_list ?? true;

        return $merged;
    }

    /**
     * @param  array<string, mixed>  ...$layers
     * @return array<string, mixed>
     */
    private function mergeSettings(array ...$layers): array
    {
        $merged = array_shift($layers) ?? [];

        foreach ($layers as $layer) {
            foreach ($layer as $key => $value) {
                if ($key === 'break_types' && is_array($value)) {
                    $merged['break_types'] = array_replace_recursive($merged['break_types'] ?? [], $value);
                } elseif (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = array_replace_recursive($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    public function rotaMode(BranchKiosk $kiosk): RotaEnforcementMode
    {
        $settings = $this->forKiosk($kiosk);

        return RotaEnforcementMode::tryFrom((string) ($settings['rota_enforcement'] ?? 'disabled'))
            ?? RotaEnforcementMode::Disabled;
    }

    /**
     * @return list<array{value: string, label: string, config: array<string, mixed>}>
     */
    public function enabledBreakTypesForOrganization(int $organizationId): array
    {
        return app(KioskBreakTypeService::class)->kioskOptions($organizationId);
    }

    /**
     * @return list<array{value: string, label: string, config: array<string, mixed>}>
     */
    public function enabledBreakTypes(BranchKiosk $kiosk): array
    {
        $settings = $this->forKiosk($kiosk);
        $types = [];

        foreach (BreakType::cases() as $type) {
            $config = (array) data_get($settings, 'break_types.'.$type->value, $type->defaultConfig());
            if (! ($config['enabled'] ?? true)) {
                continue;
            }
            $types[] = [
                'value' => $type->value,
                'label' => $type->label(),
                'config' => $config,
            ];
        }

        return $types;
    }
}
