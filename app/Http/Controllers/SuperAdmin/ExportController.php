<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $type = $request->string('type')->toString() ?: 'businesses';

        return match ($type) {
            'users' => $this->csv('users.csv', ['Name', 'Email', 'Role', 'Status', 'Created'], User::query()->with('role')->latest()->get()->map(fn (User $user) => [
                $user->name,
                $user->email,
                $user->role?->name ?? '',
                $user->status,
                $user->created_at?->toDateString() ?? '',
            ])->all()),
            'subscriptions' => $this->csv('subscriptions.csv', ['Business', 'Plan', 'Status', 'Period end'], Subscription::query()->with(['organization', 'plan'])->latest()->get()->map(fn (Subscription $subscription) => [
                $subscription->organization?->name ?? '',
                $subscription->plan?->name ?? '',
                $subscription->status instanceof \BackedEnum ? $subscription->status->value : (string) $subscription->status,
                $subscription->current_period_end?->toDateString() ?? '',
            ])->all()),
            'payments' => $this->csv('payments.csv', ['Reference', 'Business', 'Amount', 'Status', 'Paid at'], Payment::query()->with('organization')->latest()->get()->map(fn (Payment $payment) => [
                $payment->provider_reference ?? 'PAY-'.$payment->id,
                $payment->organization?->name ?? '',
                (string) $payment->amount,
                $payment->status instanceof \BackedEnum ? $payment->status->value : (string) $payment->status,
                $payment->paid_at?->toDateTimeString() ?? '',
            ])->all()),
            'support' => $this->csv('tickets.csv', ['Ticket', 'Business', 'Subject', 'Priority', 'Status'], SupportTicket::query()->with('organization')->latest()->get()->map(fn (SupportTicket $ticket) => [
                $ticket->ticket_number,
                $ticket->organization?->name ?? '',
                $ticket->subject,
                $ticket->priority instanceof \BackedEnum ? $ticket->priority->value : (string) $ticket->priority,
                $ticket->status instanceof \BackedEnum ? $ticket->status->value : (string) $ticket->status,
            ])->all()),
            default => $this->csv('businesses.csv', ['Name', 'Email', 'Country', 'Status', 'Created'], Organization::query()->latest()->get()->map(fn (Organization $org) => [
                $org->name,
                $org->email ?? '',
                $org->country,
                $org->status instanceof \BackedEnum ? $org->status->value : (string) $org->status,
                $org->created_at?->toDateString() ?? '',
            ])->all()),
        };
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    private function csv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
