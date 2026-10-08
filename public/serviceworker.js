const CACHE_PREFIX = "pwa-";
const STATIC_CACHE_NAME = `${CACHE_PREFIX}static-v3`;
const RUNTIME_CACHE_NAME = `${CACHE_PREFIX}runtime-v3`;
const OFFLINE_URL = "/offline";
const BUILD_MANIFEST_URL = "/build/manifest.json";
const MAX_RUNTIME_ENTRIES = 100;

const CORE_PRECACHE_URLS = [
    OFFLINE_URL,
    "/manifest.json",
    "/images/icons/icon-48-48.png",
    "/images/icons/icon-72-72.png",
    "/images/icons/icon-96-96.png",
    "/images/icons/icon-144-144.png",
    "/images/icons/icon-192-192.png",
    "/images/icons/icon-512-512.png",
];

const PRIVATE_PATH_PREFIXES = [
    "/account",
    "/admin",
    "/api",
    "/auth",
    "/broadcasting",
    "/design-system",
    "/impersonate",
    "/knockout-matches",
    "/livewire",
    "/login",
    "/logout",
    "/password",
    "/register",
    "/results/create",
    "/sanctum",
    "/seasons",
    "/stop-impersonating",
    "/support",
];

function isSameOrigin(request) {
    return new URL(request.url).origin === self.location.origin;
}

function toAssetUrl(file) {
    if (typeof file !== "string" || file.length === 0) {
        return null;
    }

    return `/${file.replace(/^\/+/, "")}`;
}

function uniqueUrls(urls) {
    return [...new Set(urls.filter(Boolean))];
}

function isPrivatePath(url) {
    return PRIVATE_PATH_PREFIXES.some((prefix) => {
        return url.pathname === prefix || url.pathname.startsWith(`${prefix}/`);
    });
}

function isPublicDocumentResponse(response) {
    if (!response.ok || response.type !== "basic") {
        return false;
    }

    const cacheControl = response.headers.get("Cache-Control") || "";

    return /(?:^|,)\s*public\b/i.test(cacheControl)
        && !/(?:^|,)\s*(?:private|no-store|no-cache)\b/i.test(cacheControl);
}

function isCacheableAssetRequest(request, url) {
    if (!isSameOrigin(request)) {
        return false;
    }

    if (url.pathname === "/serviceworker.js") {
        return false;
    }

    const isPublicImage = request.destination === "image"
        && url.pathname.startsWith("/images/");

    return ["font", "script", "style", "worker"].includes(request.destination)
        || isPublicImage
        || url.pathname === BUILD_MANIFEST_URL
        || url.pathname === "/manifest.json";
}

async function cacheUrls(cacheName, urls) {
    const cache = await caches.open(cacheName);

    await Promise.allSettled(urls.map(async (url) => {
        const response = await fetch(url, { cache: "no-store" });

        if (!response.ok) {
            throw new Error(`Unable to precache ${url}: ${response.status}`);
        }

        await cache.put(url, response);
    }));
}

async function getBuildAssetUrls() {
    try {
        const response = await fetch(BUILD_MANIFEST_URL, { cache: "no-store" });

        if (!response.ok) {
            return [];
        }

        const manifest = await response.json();
        const urls = new Set([BUILD_MANIFEST_URL]);
        const visitedEntries = new Set();

        const visitEntry = (entry) => {
            if (!entry || typeof entry !== "object" || visitedEntries.has(entry)) {
                return;
            }

            visitedEntries.add(entry);

            const fileUrl = toAssetUrl(entry.file);

            if (fileUrl) {
                urls.add(fileUrl);
            }

            for (const cssFile of entry.css || []) {
                const cssUrl = toAssetUrl(cssFile);

                if (cssUrl) {
                    urls.add(cssUrl);
                }
            }

            for (const assetFile of entry.assets || []) {
                const assetUrl = toAssetUrl(assetFile);

                if (assetUrl) {
                    urls.add(assetUrl);
                }
            }

            for (const importedEntryName of entry.imports || []) {
                visitEntry(manifest[importedEntryName]);
            }
        };

        Object.values(manifest).forEach(visitEntry);

        return [...urls];
    } catch (_error) {
        return [BUILD_MANIFEST_URL];
    }
}

async function trimCache(cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const requests = await cache.keys();
    const requestsToDelete = requests.slice(0, Math.max(0, requests.length - maxEntries));

    await Promise.all(requestsToDelete.map((request) => cache.delete(request)));
}

async function cacheRuntimeResponse(request, response) {
    if (!response.ok || response.type !== "basic") {
        return;
    }

    try {
        const cache = await caches.open(RUNTIME_CACHE_NAME);

        await cache.put(request, response.clone());
        await trimCache(RUNTIME_CACHE_NAME, MAX_RUNTIME_ENTRIES);
    } catch (_error) {
        // Caching is best effort; a quota or storage failure must not break a live response.
    }
}

async function handleNavigation(request) {
    const requestUrl = new URL(request.url);

    try {
        const response = await fetch(request);

        if (!isPrivatePath(requestUrl) && isPublicDocumentResponse(response)) {
            await cacheRuntimeResponse(request, response);
        }

        return response;
    } catch (_error) {
        const cachedPage = await caches.match(request);

        return cachedPage || caches.match(OFFLINE_URL);
    }
}

async function handleAsset(request) {
    const cachedResponse = await caches.match(request);

    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const response = await fetch(request);

        if (isCacheableAssetRequest(request, new URL(request.url))) {
            await cacheRuntimeResponse(request, response);
        }

        return response;
    } catch (_error) {
        return cachedResponse;
    }
}

function notificationTargetUrl(value) {
    try {
        const targetUrl = new URL(value || "/account/notifications", self.location.origin);

        if (targetUrl.origin !== self.location.origin) {
            return new URL("/account/notifications", self.location.origin).href;
        }

        return targetUrl.href;
    } catch (_error) {
        return new URL("/account/notifications", self.location.origin).href;
    }
}

self.addEventListener("install", (event) => {
    event.waitUntil((async () => {
        const buildAssetUrls = await getBuildAssetUrls();

        await cacheUrls(
            STATIC_CACHE_NAME,
            uniqueUrls([...CORE_PRECACHE_URLS, ...buildAssetUrls]),
        );

        await self.skipWaiting();
    })());
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                const currentCacheNames = [STATIC_CACHE_NAME, RUNTIME_CACHE_NAME];

                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName.startsWith(CACHE_PREFIX))
                        .filter((cacheName) => !currentCacheNames.includes(cacheName))
                        .map((cacheName) => caches.delete(cacheName)),
                );
            })
            .then(() => self.clients.claim()),
    );
});

self.addEventListener("fetch", (event) => {
    if (event.request.method !== "GET" || !isSameOrigin(event.request)) {
        return;
    }

    const requestUrl = new URL(event.request.url);

    if (event.request.mode === "navigate") {
        event.respondWith(handleNavigation(event.request));

        return;
    }

    if (isCacheableAssetRequest(event.request, requestUrl)) {
        event.respondWith(handleAsset(event.request));
    }
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
                url: notificationTargetUrl(payload.url),
            },
            tag: payload.tag || undefined,
        }),
    );
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    const targetUrl = notificationTargetUrl(event.notification.data?.url);

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
