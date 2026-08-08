<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\BreakType;
use App\Enums\KioskActivityEvent;
use App\Enums\KioskSessionStatus;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\KioskBreakType;
use App\Models\KioskSession;
use App\Models\Organization;
use App\Models\OrganizationKioskSetting;
use App\Models\User;
use App\Support\Kiosk\KioskContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class KioskBreakTypeService implements ServiceInterface
{
    /**
     * @return Collection<int, KioskBreakType>
     */
    public function activeForOrganization(int $organizationId): Collection
    {
        $this->ensureDefaults($organizationId);

        return KioskBreakType::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    public function ensureDefaults(int $organizationId): void
    {
        if (KioskBreakType::query()->where('organization_id', $organizationId)->exists()) {
            return;
        }

        $defaults = [
            ['name' => 'Lunch', 'slug' => 'lunch', 'is_paid' => false, 'max_duration_minutes' => 60, 'display_order' => 1],
            ['name' => 'Jumma', 'slug' => 'jumma', 'is_paid' => true, 'max_duration_minutes' => 90, 'display_order' => 2],
            ['name' => 'Namaz', 'slug' => 'namaz', 'is_paid' => true, 'max_duration_minutes' => 30, 'display_order' => 3],
            ['name' => 'Tea Break', 'slug' => 'tea-break', 'is_paid' => true, 'max_duration_minutes' => 15, 'display_order' => 4],
        ];

        foreach ($defaults as $row) {
            KioskBreakType::query()->create(array_merge($row, [
                'organization_id' => $organizationId,
                'is_active' => true,
            ]));
        }
    }

    public function findActive(int $organizationId, string $slugOrId): ?KioskBreakType
    {
        $query = KioskBreakType::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true);

        if (is_numeric($slugOrId)) {
            return $query->where('id', (int) $slugOrId)->first();
        }

        return $query->where('slug', $slugOrId)->first();
    }

    /**
     * @return list<array{value: string, label: string, id: int, config: array<string, mixed>}>
     */
    public function kioskOptions(int $organizationId): array
    {
        return $this->activeForOrganization($organizationId)
            ->map(fn (KioskBreakType $type): array => [
                'id' => $type->id,
                'value' => $type->slug,
                'label' => $type->name,
                'config' => [
                    'enabled' => true,
                    'paid' => $type->is_paid,
                    'max_minutes' => $type->max_duration_minutes ?? 60,
                    'default_minutes' => min(15, $type->max_duration_minutes ?? 15),
                    'allow_multiple' => true,
                ],
            ])
            ->values()
            ->all();
    }

    public function legacyEnumFor(KioskBreakType $type): BreakType
    {
        return BreakType::tryFrom($type->slug) ?? BreakType::Other;
    }
}
