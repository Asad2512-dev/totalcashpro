<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    public function countByStatus(string $status): int
    {
        return $this->model->newQuery()->where('status', $status)->count();
    }

    public function countExpiringSoon(int $days = 7): int
    {
        return $this->model->newQuery()
            ->whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Trialing->value,
            ])
            ->where(function ($query) use ($days): void {
                $query->whereBetween('current_period_end', [now(), now()->addDays($days)])
                    ->orWhereBetween('trial_ends_at', [now(), now()->addDays($days)]);
            })
            ->count();
    }

    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['organization', 'plan'])
            ->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->whereHas('organization', fn ($q) => $q->where('name', 'like', $search));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function monthlyNewCounts(int $months = 12): Collection
    {
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $from = now()->startOfMonth()->subMonths($months - 1);

        return $this->model->newQuery()
            ->selectRaw("{$monthExpr} as month, COUNT(*) as total")
            ->where('created_at', '>=', $from)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
