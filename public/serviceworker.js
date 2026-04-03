const STATIC_CACHE_NAME = "pwa-static-v2";
const RUNTIME_CACHE_NAME = "pwa-runtime-v2";
const OFFLINE_URL = "/offline";
const PRECACHE_URLS = [
    "/",
    OFFLINE_URL,
    "/manifest.json",
    "/images/icons/icon-48-48.png",
    "/images/icons/icon-72-72.png",
    "/images/icons/icon-96-96.png",
    "/images/icons/icon-144-144.png",
    "/images/icons/icon-192-192.png",
    "/images/icons/icon-512-512.png",
];

self.addEventListener("install", (event) => {
    self.skipWaiting();

    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_URLS);
        }),
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith("pwa-"))
                    .filter((cacheName) => ![STATIC_CACHE_NAME, RUNTIME_CACHE_NAME].includes(cacheName))
                    .map((cacheName) => caches.delete(cacheName)),
            );
        }).then(() => self.clients.claim()),
    );
});

self.addEventListener("fetch", (event) => {
    if (event.request.method !== "GET") {
        return;
    }

    const requestUrl = new URL(event.request.url);
    const isSameOrigin = requestUrl.origin === self.location.origin;

    if (event.request.mode === "navigate") {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    if (response.ok && isSameOrigin) {
                        const responseClone = response.clone();

                        caches.open(RUNTIME_CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }

                    return response;
                })
                .catch(async () => {
                    const cachedPage = await caches.match(event.request);

                    if (cachedPage) {
                        return cachedPage;
                    }

                    return caches.match(OFFLINE_URL);
                }),
        );

        return;
    }

    if (!isSameOrigin) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const networkResponse = fetch(event.request)
                .then((response) => {
                    if (response.ok) {
                        const responseClone = response.clone();

                        caches.open(RUNTIME_CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }

                    return response;
                })
                .catch(() => cachedResponse);

            return cachedResponse || networkResponse;
        }),
    );
});

self.addEventListener("push", (event) => {
    const payload = (() => {
        try {
            return event.data ? event.data.json() : {};
        } catch (_error) {
            return {};
        }
    })();

    event.waitUntil(
        self.registration.showNotification(payload.title || "HuddsPool notification", {
            body: payload.body || "",
            icon: payload.icon || "/images/icons/icon-192-192.png",
            badge: payload.badge || "/images/icons/icon-96-96.png",
            data: {
                url: payload.url || "/account/notifications",
            },
            tag: payload.tag || undefined,
        }),
    );
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || "/account/notifications";

    event.waitUntil(
        clients.matchAll({ type: "window", includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && "focus" in client) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }

            return undefined;
        }),
    );
});
