@component('mail::message')
# Verify your email

Hi {{ $user->name }},

Please confirm your email address to secure your {{ brand_name() }} account.

@component('mail::button', ['url' => $url])
Verify email address
@endcomponent

If you did not create an account, you can ignore this email.

Thanks,<br>
{{ brand_name() }}
@endcomponent
