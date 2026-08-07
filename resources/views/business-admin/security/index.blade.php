<x-layouts.business-admin title="Account Security" active="profile">
    @include('account.security._content', [
        'securityRoutePrefix' => 'business-admin.security',
        'profileRoute' => route('business-admin.profile'),
    ])
</x-layouts.business-admin>
