<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Enums\TicketStatus;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Support\Str;

final class SupportTicketService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): SupportTicket
    {
        $data['ticket_number'] = $data['ticket_number'] ?? $this->nextNumber();
        $ticket = SupportTicket::query()->create($data);
        $this->logAdminAction('ticket.created', 'Support ticket '.$ticket->ticket_number.' created', $ticket);

        return $ticket;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SupportTicket $ticket, array $data): SupportTicket
    {
        $old = $ticket->toArray();
        $ticket->update($data);
        $this->logAdminAction('ticket.updated', 'Ticket '.$ticket->ticket_number.' updated', $ticket, $old, $ticket->fresh()?->toArray());

        return $ticket->refresh();
    }

    public function reply(SupportTicket $ticket, string $body, bool $internal = false): SupportTicketReply
    {
        $reply = SupportTicketReply::query()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $body,
            'is_internal' => $internal,
        ]);

        if ($ticket->status === TicketStatus::Closed) {
            $ticket->update(['status' => TicketStatus::Pending]);
        }

        $this->logAdminAction('ticket.replied', 'Replied to '.$ticket->ticket_number, $ticket);

        return $reply;
    }

    public function close(SupportTicket $ticket): SupportTicket
    {
        return $this->update($ticket, ['status' => TicketStatus::Closed->value]);
    }

    private function nextNumber(): string
    {
        $latest = SupportTicket::query()->latest('id')->value('ticket_number');
        $n = 1000;

        if (is_string($latest) && preg_match('/(\d+)/', $latest, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return '#'.$n;
    }
}
