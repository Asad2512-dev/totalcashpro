@component('mail::message')
# Trial ending soon

Hi {{ $user->name }},

Your **{{ $organization->name }}** trial on {{ brand_name() }} ends in **{{ $daysRemaining }} days**.

Choose a plan to keep cash up, staff, inventory and reports running without interruption.

@component('mail::button', ['url' => $choosePlanUrl])
Choose a plan
@endcomponent

Thanks,<br>
{{ brand_name() }}
@endcomponent
