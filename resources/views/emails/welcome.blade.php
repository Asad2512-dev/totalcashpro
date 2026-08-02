@component('mail::message')
# Welcome to {{ brand_name() }}

Hi {{ $user->name }},

Your business **{{ $organization->name }}** is ready on {{ brand_name() }}.

You have a **{{ $trialDays }}-day Professional trial** with full access to cash up, staff, attendance, inventory, payroll and more.

@component('mail::button', ['url' => $dashboardUrl])
Go to your dashboard
@endcomponent

Please verify your email address if you have not already — this keeps your account secure.

Thanks,<br>
{{ brand_name() }}
@endcomponent
