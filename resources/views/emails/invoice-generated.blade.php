@component('mail::message')
# New bill generated

Hi {{ $user->name }},

A recurring bill **{{ $bill->title ?? 'Bill' }}** for **£{{ number_format((float) $bill->amount, 2) }}** has been generated.

Please review it in your finance dashboard.

Thanks,<br>
{{ brand_name() }}
@endcomponent
