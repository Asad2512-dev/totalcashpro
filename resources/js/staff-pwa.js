const DB_NAME = 'totalcashpro-staff';
const STORE = 'offline_queue';
const DB_VERSION = 1;

function openDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = () => {
            const db = request.result;

            if (!db.objectStoreNames.contains(STORE)) {
                db.createObjectStore(STORE, { keyPath: 'id', autoIncrement: true });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

export async function queueOfflineAction(payload) {
    const db = await openDb();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).add({
            ...payload,
            queued_at: new Date().toISOString(),
        });
        tx.oncomplete = () => resolve(true);
        tx.onerror = () => reject(tx.error);
    });
}

export async function flushOfflineQueue() {
    if (!navigator.onLine) {
        return 0;
    }

    const db = await openDb();
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) {
        return 0;
    }

    const items = await new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readonly');
        const request = tx.objectStore(STORE).getAll();
        request.onsuccess = () => resolve(request.result || []);
        request.onerror = () => reject(request.error);
    });

    let flushed = 0;

    for (const item of items) {
        try {
            const response = await fetch(item.url, {
                method: item.method || 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    Accept: 'application/json',
                },
                body: JSON.stringify(item.body || {}),
            });

            if (response.ok) {
                await new Promise((resolve, reject) => {
                    const tx = db.transaction(STORE, 'readwrite');
                    tx.objectStore(STORE).delete(item.id);
                    tx.oncomplete = () => resolve(true);
                    tx.onerror = () => reject(tx.error);
                });
                flushed++;
            }
        } catch {
            break;
        }
    }

    return flushed;
}

export function registerStaffPwa() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/staff-sw.js').catch(() => {});
    });

    navigator.serviceWorker?.addEventListener('message', (event) => {
        if (event.data?.type === 'STAFF_SYNC_OFFLINE_QUEUE') {
            flushOfflineQueue();
        }
    });

    window.addEventListener('online', () => {
        flushOfflineQueue();

        if ('sync' in navigator.serviceWorker?.ready) {
            navigator.serviceWorker.ready.then((registration) => {
                registration.sync.register('staff-offline-queue').catch(() => {});
            });
        }
    });
}
