@component('mail::message')
# Reset your password

Hi {{ $user->name }},

You requested a password reset for your {{ brand_name() }} account.

@component('mail::button', ['url' => $url])
Reset password
@endcomponent

This link expires in {{ $count }} minutes. If you did not request a reset, no action is required.

Thanks,<br>
{{ brand_name() }}
@endcomponent
