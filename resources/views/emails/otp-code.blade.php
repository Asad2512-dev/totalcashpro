@component('mail::message')
# Verification code

Hi {{ $user->name }},

Use this code to complete **{{ $purpose }}**:

@component('mail::panel')
## {{ $code }}
@endcomponent

This code expires in **{{ $expiryMinutes }} minutes**. If you did not request this, you can ignore this email.

Thanks,<br>
{{ brand_name() }}
@endcomponent
