@component('mail::message')
# Subscription expired

Hi {{ $user->name }},

Your {{ brand_name() }} subscription has expired. Renew to restore full access for your team.

@component('mail::button', ['url' => $renewUrl])
Renew subscription
@endcomponent

Thanks,<br>
{{ brand_name() }}
@endcomponent
