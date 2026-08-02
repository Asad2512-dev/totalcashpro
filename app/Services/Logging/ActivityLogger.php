<?php

declare(strict_types=1);

namespace App\Services\Logging;

use App\Contracts\ServiceInterface;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class ActivityLogger implements ServiceInterface
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(
        string $event,
        string $description,
        ?User $actor = null,
        ?Model $subject = null,
        array $properties = [],
    ): ActivityLog {
        return ActivityLog::query()->create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'System',
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'created_at' => now(),
        ]);
    }
}
