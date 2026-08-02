<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\AccessRequestStatus;
use App\Enums\OrganizationStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\AccessRequest;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

final class OrganizationRepository extends BaseRepository implements OrganizationRepositoryInterface
{
    public function __construct(Organization $model)
    {
        parent::__construct($model);
    }

    public function countAll(): int
    {
        return $this->model->newQuery()->count();
    }

    public function countByStatus(string $status): int
    {
        return $this->model->newQuery()->where('status', $status)->count();
    }

    public function countTrialing(): int
    {
        return $this->model->newQuery()
            ->where(function ($query): void {
                $query->where('status', OrganizationStatus::Trial->value)
                    ->orWhereHas(
                        'subscriptions',
                        fn ($subscriptionQuery) => $subscriptionQuery->where('status', SubscriptionStatus::Trialing->value),
                    );
            })
            ->count();
    }

    public function countActivePaid(): int
    {
        return $this->model->newQuery()
            ->where('status', OrganizationStatus::Active->value)
            ->whereHas('subscriptions', fn ($q) => $q->where('status', SubscriptionStatus::Active->value))
            ->count();
    }

    public function latestWithRelations(int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->with(['owner', 'currentSubscription.plan'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['owner', 'currentSubscription.plan'])
            ->withCount('branches');

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('slug', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort = in_array($filters['sort'] ?? null, ['name', 'created_at', 'status', 'country'], true)
            ? $filters['sort']
            : 'created_at';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        return $query->paginate($perPage)->withQueryString();
    }

    public function countExpiredSubscriptions(): int
    {
        return $this->model->newQuery()
            ->whereHas(
                'subscriptions',
                fn ($q) => $q->where('status', SubscriptionStatus::Expired->value),
            )
            ->count();
    }

    public function countActiveBranches(): int
    {
        return Branch::query()->where('status', 'open')->count();
    }

    public function countActiveStaff(): int
    {
        return User::query()
            ->where('status', 'active')
            ->whereHas('role', fn ($q) => $q->where('slug', RoleSlug::Staff->value))
            ->count();
    }

    public function countPendingRequests(): int
    {
        return AccessRequest::query()
            ->where('status', AccessRequestStatus::Pending->value)
            ->count();
    }

    public function monthlySignupCounts(int $months = 12): SupportCollection
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
