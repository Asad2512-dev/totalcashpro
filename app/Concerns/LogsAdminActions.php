<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\User;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use Illuminate\Database\Eloquent\Model;

trait LogsAdminActions
{
    protected function activityLogger(): ActivityLogger
    {
        return app(ActivityLogger::class);
    }

    protected function auditLogger(): AuditLogger
    {
        return app(AuditLogger::class);
    }

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    protected function logAdminAction(
        string $event,
        string $description,
        ?Model $subject = null,
        ?array $old = null,
        ?array $new = null,
        ?User $actor = null,
    ): void {
        $actor ??= auth()->user();

        $this->activityLogger()->log($event, $description, $actor, $subject, $new ?? []);
        $this->auditLogger()->log($event, $actor, $subject, $old, $new);
    }
}
