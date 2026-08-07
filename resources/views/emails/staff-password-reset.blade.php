@component('mail::message')
# Password reset

Hi {{ $user->name }},

**{{ $resetBy->name }}** reset your {{ brand_name() }} staff account password.

**Sign-in email:** {{ $user->email }}  
**Temporary password:** {{ $temporaryPassword }}

Please sign in and change your password immediately.

@component('mail::button', ['url' => $loginUrl])
Sign in
@endcomponent

Thanks,<br>
{{ brand_name() }}
@endcomponent
