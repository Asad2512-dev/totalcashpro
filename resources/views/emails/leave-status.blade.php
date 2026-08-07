@component('mail::message')
@if ($approved)
# Leave request approved
@else
# Leave request declined
@endif

Hi {{ $user->name }},

@if ($approved)
Your leave from **{{ $startDate }}** to **{{ $endDate }}** has been approved.
@else
Your leave from **{{ $startDate }}** to **{{ $endDate }}** was declined.
@if ($adminNotes)
**Note:** {{ $adminNotes }}
@endif
@endif

Thanks,<br>
{{ brand_name() }}
@endcomponent
