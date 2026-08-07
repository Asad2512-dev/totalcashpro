@component('mail::message')
# You're invited to {{ brand_name() }}

Hi {{ $user->name }},

**{{ $invitedBy->name }}** has invited you to join their team on {{ brand_name() }}.

**Sign-in email:** {{ $user->email }}  
**Temporary password:** {{ $temporaryPassword }}

Please sign in and change your password immediately.

@component('mail::button', ['url' => $loginUrl])
Sign in
@endcomponent

Thanks,<br>
{{ brand_name() }}
@endcomponent
