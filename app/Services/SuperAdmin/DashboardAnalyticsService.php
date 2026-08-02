<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\OrganizationStatus;
use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\Organization;
use App\Models\SupportTicket;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

final class DashboardAnalyticsService implements ServiceInterface
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organizations,
        private readonly PaymentRepositoryInterface $payments,
        private readonly SubscriptionRepositoryInterface $subscriptions,
    ) {}

    /**
     * @return list<array{label: string, value: string, change: string, tone: string}>
     */
    public function stats(): array
    {
        $totalBusinesses = $this->organizations->countAll();
        $activeBusinesses = $this->organizations->countByStatus(OrganizationStatus::Active->value);
        $pendingBusinesses = $this->organizations->countByStatus(OrganizationStatus::Pending->value);
        $trialBusinesses = $this->organizations->countByStatus(OrganizationStatus::Trial->value);
        $cancelledBusinesses = $this->organizations->countByStatus(OrganizationStatus::Cancelled->value);
        $suspendedBusinesses = $this->organizations->countByStatus(OrganizationStatus::Suspended->value);
        $expiredBusinesses = $this->organizations->countExpiredSubscriptions();
        $paidUsers = $this->organizations->countActivePaid();
        $todaySignups = Organization::query()->whereDate('created_at', today())->count();
        $monthlyRevenue = $this->payments->sumPaid(now()->startOfMonth(), now()->endOfMonth());
        $yearlyRevenue = $this->payments->sumPaid(now()->startOfYear(), now()->endOfYear());
        $paymentsToday = $this->payments->sumPaidToday();
        $paymentsTodayCount = $this->payments->countPaidToday();
        $openTickets = SupportTicket::query()->where('status', TicketStatus::Open->value)->count();
        $unreadNotifications = AppNotification::query()->whereNull('read_at')->whereNull('archived_at')->count();
        $expiringSoon = $this->subscriptions->countExpiringSoon(7);
        $pendingRequests = $this->organizations->countPendingRequests();

        return [
            ['label' => 'Total Businesses', 'value' => (string) $totalBusinesses, 'change' => $this->monthDeltaLabel(), 'tone' => 'success'],
            ['label' => 'Active Businesses', 'value' => (string) $activeBusinesses, 'change' => 'Live accounts', 'tone' => 'success'],
            ['label' => 'Pending Businesses', 'value' => (string) $pendingBusinesses, 'change' => 'Awaiting setup', 'tone' => 'warning'],
            ['label' => 'Trial Businesses', 'value' => (string) $trialBusinesses, 'change' => $expiringSoon.' ending soon', 'tone' => 'info'],
            ['label' => 'Paid Businesses', 'value' => (string) $paidUsers, 'change' => 'Active subscriptions', 'tone' => 'success'],
            ['label' => 'Cancelled Businesses', 'value' => (string) $cancelledBusinesses, 'change' => (string) $suspendedBusinesses.' suspended', 'tone' => 'danger'],
            ['label' => 'Expired Businesses', 'value' => (string) $expiredBusinesses, 'change' => 'Expired subscriptions', 'tone' => 'danger'],
            ['label' => "Today's Signups", 'value' => (string) $todaySignups, 'change' => today()->toFormattedDateString(), 'tone' => 'info'],
            ['label' => 'Monthly Revenue', 'value' => $this->money($monthlyRevenue), 'change' => now()->format('M Y'), 'tone' => 'success'],
            ['label' => 'Yearly Revenue', 'value' => $this->money($yearlyRevenue), 'change' => 'YTD', 'tone' => 'info'],
            ['label' => "Today's Payments", 'value' => $this->money($paymentsToday), 'change' => $paymentsTodayCount.' charges', 'tone' => 'success'],
            ['label' => 'Pending Tickets', 'value' => (string) $openTickets, 'change' => $pendingRequests.' access requests', 'tone' => $openTickets > 0 ? 'danger' : 'success'],
            ['label' => 'Unread Notifications', 'value' => (string) $unreadNotifications, 'change' => 'Inbox', 'tone' => 'warning'],
            ['label' => 'Expiring Subscriptions', 'value' => (string) $expiringSoon, 'change' => 'Next 7 days', 'tone' => 'warning'],
        ];
    }

    /**
     * @return list<int>
     */
    public function monthlyRevenueBars(int $months = 12): array
    {
        return $this->toBars($this->payments->monthlyRevenue($months), $months);
    }

    /**
     * @return list<int>
     */
    public function subscriptionGrowthBars(int $months = 12): array
    {
        return $this->toBars($this->subscriptions->monthlyNewCounts($months), $months);
    }

    /**
     * @return list<array{business: string, plan: string, status: string, amount: string, owner: string}>
     */
    public function recentBusinesses(int $limit = 8): array
    {
        return $this->organizations->latestWithRelations($limit)->map(function ($organization): array {
            $plan = $organization->currentSubscription?->plan;
            $amount = $plan ? $plan->formattedPrice() : '—';

            return [
                'business' => $organization->name,
                'owner' => $organization->owner?->name ?? '—',
                'plan' => $plan?->name ?? '—',
                'status' => $organization->status instanceof \BackedEnum
                    ? $organization->status->label()
                    : ucfirst((string) $organization->status),
                'amount' => $amount === 'Custom' ? $amount : ($amount !== '—' ? $amount : '£0.00'),
            ];
        })->all();
    }

    /**
     * @return list<array{time: string, actor: string, action: string}>
     */
    public function recentActivity(int $limit = 8): array
    {
        return ActivityLog::query()
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (ActivityLog $log): array => [
                'time' => $log->created_at?->diffForHumans() ?? '—',
                'actor' => $log->actor_name ?? 'System',
                'action' => $log->description,
            ])
            ->all();
    }

    /**
     * @return list<array{invoice: string, business: string, amount: string, status: string}>
     */
    public function latestPayments(int $limit = 8): array
    {
        return $this->payments->latest($limit)->map(function ($payment): array {
            return [
                'invoice' => $payment->invoice?->number ?? ($payment->provider_reference ?? 'PAY-'.$payment->id),
                'business' => $payment->organization?->name ?? '—',
                'amount' => $payment->formattedAmount(),
                'status' => $payment->status instanceof PaymentStatus
                    ? $payment->status->label()
                    : ucfirst((string) $payment->status),
            ];
        })->all();
    }

    /**
     * @return list<array{ticket: string, business: string, subject: string, priority: string, status: string}>
     */
    public function recentTickets(int $limit = 8): array
    {
        return SupportTicket::query()
            ->with('organization')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (SupportTicket $ticket): array => [
                'ticket' => $ticket->ticket_number,
                'business' => $ticket->organization?->name ?? '—',
                'subject' => $ticket->subject,
                'priority' => $ticket->priority->label(),
                'status' => ucfirst($ticket->status->value),
            ])
            ->all();
    }

    private function money(float $amount): string
    {
        return '£'.number_format($amount, 2);
    }

    private function monthDeltaLabel(): string
    {
        $count = Organization::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return $count > 0 ? '+'.$count.' this month' : 'No new this month';
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object{month: string, total: float|int|string}>  $rows
     * @return list<int>
     */
    private function toBars($rows, int $months): array
    {
        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->month] = (float) $row->total;
        }

        $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = now()->startOfMonth()->subMonths($i)->format('Y-m');
            $values[] = $map[$key] ?? 0.0;
        }

        $max = max($values) ?: 0;

        if ($max <= 0) {
            return [];
        }

        return array_map(
            static fn (float $value): int => max(8, (int) round(($value / $max) * 100)),
            $values,
        );
    }
}
