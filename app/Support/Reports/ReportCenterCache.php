<?php

declare(strict_types=1);

namespace App\Support\Reports;

use Illuminate\Support\Facades\Cache;

/**
 * Bumps a per-organization version so cached report snapshots are discarded immediately.
 */
final class ReportCenterCache
{
    private const VERSION_PREFIX = 'report_center_version:';

    public static function version(int $organizationId): int
    {
        return (int) Cache::get(self::VERSION_PREFIX.$organizationId, 1);
    }

    public static function bump(int $organizationId): void
    {
        $key = self::VERSION_PREFIX.$organizationId;

        if (! Cache::has($key)) {
            Cache::forever($key, 2);

            return;
        }

        Cache::increment($key);
    }
}
