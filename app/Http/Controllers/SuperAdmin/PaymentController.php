<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PaymentController extends Controller
{
    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Record Payment',
            'active' => 'payments',
            'action' => route('super-admin.payments.store'),
            'cancelRoute' => route('super-admin.payments'),
            'fields' => [
                ['name' => 'organization_id', 'type' => 'select', 'label' => 'Business', 'options' => Organization::query()->orderBy('name')->pluck('name', 'id')->all()],
                ['name' => 'amount', 'type' => 'number', 'value' => '29.99'],
                ['name' => 'currency', 'value' => 'GBP'],
                ['name' => 'provider', 'type' => 'select', 'options' => ['manual' => 'Manual', 'stripe' => 'Stripe']],
                ['name' => 'method', 'value' => 'card'],
                ['name' => 'provider_reference', 'label' => 'Reference'],
                ['name' => 'status', 'type' => 'select', 'value' => 'paid', 'options' => [
                    'paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'refunded' => 'Refunded', 'cancelled' => 'Cancelled',
                ]],
            ],
        ]);
    }

    public function store(Request $request, ActivityLogger $activity, AuditLogger $audit): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'provider' => ['required', 'in:manual,stripe'],
            'method' => ['nullable', 'string', 'max:50'],
            'provider_reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:paid,pending,failed,refunded,cancelled'],
        ]);

        $invoice = Invoice::query()->create([
            'organization_id' => $data['organization_id'],
            'number' => 'INV-'.now()->format('ymd').'-'.random_int(1000, 9999),
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'] === 'paid' ? 'paid' : 'pending',
            'paid_at' => $data['status'] === 'paid' ? now() : null,
            'due_at' => now(),
        ]);

        $payment = Payment::query()->create([
            ...$data,
            'invoice_id' => $invoice->id,
            'paid_at' => $data['status'] === 'paid' ? now() : null,
        ]);

        $activity->log('payment.recorded', 'Payment recorded: '.$payment->formattedAmount(), auth()->user(), $payment);
        $audit->log('payment.recorded', auth()->user(), $payment, null, $payment->toArray());

        return redirect()->route('super-admin.payments.show', $payment)->with('status', 'Payment recorded.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['organization', 'invoice']);

        return view('admin.crud.show', [
            'title' => $payment->invoice?->number ?? 'Payment #'.$payment->id,
            'active' => 'payments',
            'backRoute' => route('super-admin.payments'),
            'fields' => [
                ['label' => 'Business', 'value' => $payment->organization?->name ?? '—'],
                ['label' => 'Amount', 'value' => $payment->formattedAmount()],
                ['label' => 'Status', 'value' => $payment->status?->label() ?? '—'],
                ['label' => 'Provider', 'value' => ucfirst($payment->provider)],
                ['label' => 'Method', 'value' => $payment->method ?? '—'],
                ['label' => 'Reference', 'value' => $payment->provider_reference ?? '—'],
                ['label' => 'Paid at', 'value' => $payment->paid_at?->format('d M Y H:i') ?? '—'],
            ],
            'actions' => view('admin.partials.payment-actions', compact('payment')),
        ]);
    }

    public function markStatus(Request $request, Payment $payment, ActivityLogger $activity): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:paid,pending,failed,refunded,cancelled']]);
        $status = PaymentStatus::from($data['status']);
        $payment->update([
            'status' => $status,
            'paid_at' => $status === PaymentStatus::Paid ? ($payment->paid_at ?? now()) : $payment->paid_at,
        ]);
        $activity->log('payment.status_changed', 'Payment marked '.$status->label(), auth()->user(), $payment);

        return back()->with('status', 'Payment status updated.');
    }
}
