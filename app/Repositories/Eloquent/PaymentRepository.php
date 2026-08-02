<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

final class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function sumPaid(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float
    {
        $query = $this->model->newQuery()->where('status', PaymentStatus::Paid->value);

        if ($from) {
            $query->where('paid_at', '>=', $from);
        }

        if ($to) {
            $query->where('paid_at', '<=', $to);
        }

        return (float) $query->sum('amount');
    }

    public function sumPaidToday(): float
    {
        return $this->sumPaid(now()->startOfDay(), now()->endOfDay());
    }

    public function countPaidToday(): int
    {
        return $this->model->newQuery()
            ->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
    }

    public function latest(int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->with(['organization', 'invoice'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['organization', 'invoice'])
            ->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('provider_reference', 'like', $search)
                    ->orWhereHas('organization', fn ($q) => $q->where('name', 'like', $search))
                    ->orWhereHas('invoice', fn ($q) => $q->where('number', 'like', $search));
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function monthlyRevenue(int $months = 12): SupportCollection
    {
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', paid_at)"
            : "DATE_FORMAT(paid_at, '%Y-%m')";

        $from = now()->startOfMonth()->subMonths($months - 1);

        return $this->model->newQuery()
            ->selectRaw("{$monthExpr} as month, COALESCE(SUM(amount), 0) as total")
            ->where('status', PaymentStatus::Paid->value)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $from)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
