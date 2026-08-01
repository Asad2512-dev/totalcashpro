New contact message submitted on TotalCashPro.

Name: {{ $contactMessage->name }}
Email: {{ $contactMessage->email }}
Phone: {{ $contactMessage->phone ?: '—' }}
Subject: {{ $contactMessage->subject }}

Message:
{{ $contactMessage->message }}

Submitted at: {{ $contactMessage->created_at?->toDateTimeString() }}
