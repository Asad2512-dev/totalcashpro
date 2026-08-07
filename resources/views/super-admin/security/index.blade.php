<x-layouts.admin title="Account Security" active="profile">
    @include('account.security._content', [
        'securityRoutePrefix' => 'super-admin.security',
        'profileRoute' => route('super-admin.profile.edit'),
    ])
</x-layouts.admin>
