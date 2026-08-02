<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Coupon;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\SupportTicket;
use App\Models\User;

final class GlobalSearchService implements ServiceInterface
{
    /**
     * @return list<array{group: string, label: string, url: string}>
     */
    public function search(string $query, int $limit = 8): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $like = '%'.$query.'%';
        $results = [];

        Organization::query()->where('name', 'like', $like)->orWhere('email', 'like', $like)
            ->limit($limit)->get()->each(function (Organization $org) use (&$results): void {
                $results[] = [
                    'group' => 'Businesses',
                    'label' => $org->name,
                    'url' => route('super-admin.organizations.show', $org),
                ];
            });

        User::query()->where('name', 'like', $like)->orWhere('email', 'like', $like)
            ->limit($limit)->get()->each(function (User $user) use (&$results): void {
                $results[] = [
                    'group' => 'Users',
                    'label' => $user->name.' · '.$user->email,
                    'url' => route('super-admin.users.show', $user),
                ];
            });

        Plan::query()->where('name', 'like', $like)->limit($limit)->get()
            ->each(function (Plan $plan) use (&$results): void {
                $results[] = [
                    'group' => 'Plans',
                    'label' => $plan->name,
                    'url' => route('super-admin.plans.edit', $plan),
                ];
            });

        Coupon::query()->where('code', 'like', $like)->limit($limit)->get()
            ->each(function (Coupon $coupon) use (&$results): void {
                $results[] = [
                    'group' => 'Coupons',
                    'label' => $coupon->code,
                    'url' => route('super-admin.coupons.edit', $coupon),
                ];
            });

        Payment::query()->where('provider_reference', 'like', $like)->limit($limit)->get()
            ->each(function (Payment $payment) use (&$results): void {
                $results[] = [
                    'group' => 'Payments',
                    'label' => ($payment->provider_reference ?? 'PAY-'.$payment->id).' · '.$payment->formattedAmount(),
                    'url' => route('super-admin.payments.show', $payment),
                ];
            });

        SupportTicket::query()->where('ticket_number', 'like', $like)->orWhere('subject', 'like', $like)
            ->limit($limit)->get()->each(function (SupportTicket $ticket) use (&$results): void {
                $results[] = [
                    'group' => 'Tickets',
                    'label' => $ticket->ticket_number.' · '.$ticket->subject,
                    'url' => route('super-admin.support.show', $ticket),
                ];
            });

        return $results;
    }
}
