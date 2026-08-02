<x-layouts.auth title="Reset password" :seo="['title' => 'Reset password — TotalCashPro', 'description' => 'Choose a new password for your TotalCashPro account.']">
    <div class="mx-auto w-full max-w-md">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Security</p>
        <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-gray-900">Set a new password</h2>
        <p class="mt-2 text-sm text-gray-500">Choose a strong password you haven’t used elsewhere.</p>

        @if ($errors->any())
            <div class="mt-6">
                <x-admin.alert tone="danger">{{ $errors->first() }}</x-admin.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <x-admin.input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">New password</label>
                <x-admin.input type="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Confirm password</label>
                <x-admin.input type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <x-admin.button type="submit" class="w-full">Reset password</x-admin.button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:underline">Back to sign in</a>
        </p>
    </div>
</x-layouts.auth>
