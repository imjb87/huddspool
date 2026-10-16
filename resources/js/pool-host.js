const host = document.querySelector('[data-pool-host]');
if (host) {
    const frame = host.querySelector('[data-pool-frame]');
    const abort = new AbortController();
    window.addEventListener('message', event => {
        if (event.origin !== location.origin || event.source !== frame.contentWindow) return;
        if (event.data?.type === 'huddspool:ready') {
            frame.contentWindow.postMessage({ type: 'huddspool:init', config: {
                players: [
                    { id: host.dataset.playerId, name: host.dataset.playerName, avatar: host.dataset.playerAvatar },
                    { id: 'local-2', name: 'Player 2' },
                ],
                storageId: host.dataset.playerId,
                returnUrl: host.dataset.returnUrl,
            } }, location.origin);
        }
        if (event.data?.type === 'huddspool:event') {
            host.dispatchEvent(new CustomEvent(`huddspool:${event.data.event.type}`, { bubbles: true, detail: event.data.event }));
        }
    }, { signal: abort.signal });
    // Also initializes an already-loaded frame if Vite arrived after its ready message.
    frame.addEventListener('load', () => frame.contentWindow.postMessage({ type: 'huddspool:host-ready' }, location.origin), { signal: abort.signal });
    frame.contentWindow.postMessage({ type: 'huddspool:host-ready' }, location.origin);
    window.addEventListener('pagehide', event => {
        if (!event.persisted) {
            frame.contentWindow?.huddspoolGame?.dispose();
            abort.abort();
        }
    }, { signal: abort.signal });
}
