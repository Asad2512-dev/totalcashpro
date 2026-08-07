{{-- Apply theme before CSS/JS load to prevent dark-mode white flash (FOUC). --}}
<script>
(function (doc) {
    var key = 'tcp-admin-theme';
    var stored = null;

    try {
        stored = doc.defaultView.localStorage.getItem(key);
    } catch (e) {}

    var dark = stored === 'dark'
        || (stored !== 'light'
            && doc.defaultView.matchMedia
            && doc.defaultView.matchMedia('(prefers-color-scheme: dark)').matches);

    var root = doc.documentElement;

    if (dark) {
        root.classList.add('dark');
        root.style.colorScheme = 'dark';
    } else {
        root.classList.remove('dark');
        root.style.colorScheme = 'light';
    }

    var meta = doc.querySelector('meta[name="theme-color"]');

    if (meta) {
        meta.setAttribute('content', dark ? '#030712' : '#16A34A');
    }
})(document);
</script>
<style>
    html {
        background-color: #f9fafb;
        color-scheme: light;
    }

    html.dark {
        background-color: #030712;
        color-scheme: dark;
    }

    body {
        background-color: inherit;
    }
</style>
