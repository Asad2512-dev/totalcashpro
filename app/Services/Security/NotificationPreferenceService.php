<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Enums\NotificationCategory;
use App\Models\NotificationPreference;
use App\Models\User;

final class NotificationPreferenceService
{
    /**
     * @return array<string, array{email: bool, database: bool, label: string}>
     */
    public function allForUser(User $user): array
    {
        $stored = NotificationPreference::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy(fn (NotificationPreference $p) => $p->category->value);

        $result = [];

        foreach (NotificationCategory::cases() as $category) {
            $pref = $stored->get($category->value);
            $result[$category->value] = [
                'email' => $pref?->email_enabled ?? true,
                'database' => $pref?->database_enabled ?? true,
                'label' => str($category->value)->replace('_', ' ')->title()->toString(),
            ];
        }

        return $result;
    }

    public function wantsEmail(User $user, NotificationCategory $category): bool
    {
        return $this->preference($user, $category)?->email_enabled ?? true;
    }

    public function wantsDatabase(User $user, NotificationCategory $category): bool
    {
        return $this->preference($user, $category)?->database_enabled ?? true;
    }

    /**
     * @param  array<string, array{email?: bool, database?: bool}>  $preferences
     */
    public function sync(User $user, array $preferences): void
    {
        foreach ($preferences as $categoryValue => $settings) {
            $category = NotificationCategory::from($categoryValue);

            NotificationPreference::query()->updateOrCreate(
                ['user_id' => $user->id, 'category' => $category],
                [
                    'email_enabled' => (bool) ($settings['email'] ?? true),
                    'database_enabled' => (bool) ($settings['database'] ?? true),
                ],
            );
        }
    }

    private function preference(User $user, NotificationCategory $category): ?NotificationPreference
    {
        return NotificationPreference::query()
            ->where('user_id', $user->id)
            ->where('category', $category)
            ->first();
    }
}
