const CACHE_NAME = 'totalcashpro-staff-v1';
const OFFLINE_URLS = ['/staff', '/staff/clock', '/staff/cash-up'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(OFFLINE_URLS)).then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))),
        ).then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    if (!url.pathname.startsWith('/staff')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));

                return response;
            })
            .catch(() => caches.match(event.request).then((cached) => cached || caches.match('/staff'))),
    );
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'staff-offline-queue') {
        event.waitUntil(flushOfflineQueue());
    }
});

async function flushOfflineQueue() {
    const clients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });

    clients.forEach((client) => {
        client.postMessage({ type: 'STAFF_SYNC_OFFLINE_QUEUE' });
    });
}
