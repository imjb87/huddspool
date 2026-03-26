
import _ from 'lodash';

window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.Pusher = Pusher;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

if (import.meta.env.VITE_REVERB_APP_KEY) {
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'https';
    const forceTls = reverbScheme === 'https' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
        forceTLS: forceTls,
        enabledTransports: ['ws', 'wss'],
    });
}

window.resultFormCollaboration = ({ componentId, channelName, clientId }) => ({
    init() {
        if (!window.Echo || !window.Livewire) {
            console.warn('[result-collaboration] Echo or Livewire is unavailable; collaboration channel was not initialized.', {
                channelName,
            });

            return;
        }

        const component = () => window.Livewire.find(componentId);

        window.Echo.leave(channelName);

        window.Echo.join(channelName)
            .here((members) => {
                console.info('[result-collaboration] Connected to broadcast channel.', {
                    channelName,
                    members,
                });

                component()?.call('syncCollaborators', members);
            })
            .joining((member) => component()?.call('collaboratorJoined', member))
            .leaving((member) => component()?.call('collaboratorLeft', member))
            .listen('.league-result.draft-updated', (event) => {
                if (event.client_id === clientId) {
                    return;
                }

                component()?.call('syncDraftFromBroadcast', event);
            })
            .listen('.league-result.submitted', (event) => {
                if (event.client_id === clientId || !event.result_url) {
                    return;
                }

                window.location.assign(event.result_url);
            });
    },
});

window.resultFormFlashRow = (frameNumber) => ({
    isFlashing: false,
    flashTimeoutId: null,
    flashIfIncluded(frameNumbers) {
        if (!Array.isArray(frameNumbers) || !frameNumbers.includes(frameNumber)) {
            return;
        }

        this.isFlashing = false;

        window.requestAnimationFrame(() => {
            this.isFlashing = true;

            if (this.flashTimeoutId) {
                window.clearTimeout(this.flashTimeoutId);
            }

            this.flashTimeoutId = window.setTimeout(() => {
                this.isFlashing = false;
            }, 1200);
        });
    },
});
