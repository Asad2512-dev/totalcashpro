<x-layouts.staff title="Account Security" active="profile">
    @include('account.security._content', [
        'securityRoutePrefix' => 'staff.security',
        'profileRoute' => route('staff.profile'),
    ])
</x-layouts.staff>
