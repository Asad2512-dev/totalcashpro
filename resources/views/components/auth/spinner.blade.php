<div
    x-data
    x-show="$store.authUi.submitting"
    x-cloak
    x-transition.opacity.duration.200ms
    class="auth-loader-overlay"
    role="status"
    aria-busy="true"
    aria-label="Loading"
>
    <div class="auth-loader-spinner"></div>
</div>
