@component('mail::message')
@if ($approved)
# Shift swap approved
@else
# Shift swap declined
@endif

Hi {{ $user->name }},

@if ($approved)
Your shift swap for **{{ $shiftDate }}** has been approved.
@if ($partnerName)
**Colleague:** {{ $partnerName }}
@endif
@else
Your shift swap for **{{ $shiftDate }}** was declined.
@endif

Thanks,<br>
{{ brand_name() }}
@endcomponent
