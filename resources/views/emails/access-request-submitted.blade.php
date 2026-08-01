New access request submitted for TotalCashPro.

Business Name: {{ $accessRequest->business_name }}
Owner Name: {{ $accessRequest->owner_name }}
Email: {{ $accessRequest->email }}
Phone: {{ $accessRequest->phone }}
Business Address: {{ $accessRequest->business_address ?: '—' }}
Country: {{ $accessRequest->country }}
Business Type: {{ $accessRequest->business_type }}
Number of Employees: {{ $accessRequest->number_of_employees }}
Selected Plan: {{ $accessRequest->selected_plan->label() }} ({{ $accessRequest->selected_plan->priceLabel() }})
Additional Notes: {{ $accessRequest->additional_notes ?: '—' }}

Submitted at: {{ $accessRequest->created_at?->toDateTimeString() }}
