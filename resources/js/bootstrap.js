
const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';
let echoLoader = null;

const isPlainObject = (value) => Object.prototype.toString.call(value) === '[object Object]';

const isDeepEqual = (left, right) => {
    if (Object.is(left, right)) {
        return true;
    }

    if (Array.isArray(left) && Array.isArray(right)) {
        if (left.length !== right.length) {
            return false;
        }

        return left.every((value, index) => isDeepEqual(value, right[index]));
    }

    if (isPlainObject(left) && isPlainObject(right)) {
        const leftKeys = Object.keys(left);
        const rightKeys = Object.keys(right);

        if (leftKeys.length !== rightKeys.length) {
            return false;
        }

        return leftKeys.every((key) => rightKeys.includes(key) && isDeepEqual(left[key], right[key]));
    }

    return false;
};

const request = async (url, { method = 'GET', body = null } = {}) => {
    const response = await window.fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: body ? JSON.stringify(body) : null,
    });

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}.`);
    }

    return response;
};

window.ensureEcho = async () => {
    if (window.Echo) {
        return window.Echo;
    }

    if (!import.meta.env.VITE_REVERB_APP_KEY) {
        return null;
    }

    if (echoLoader) {
        return echoLoader;
    }

    echoLoader = Promise.all([
        import('laravel-echo'),
        import('pusher-js'),
    ]).then(([{ default: Echo }, { default: Pusher }]) => {
        const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'https';
        const forceTls = reverbScheme === 'https' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1';

        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
            wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
            forceTLS: forceTls,
            enabledTransports: ['ws', 'wss'],
        });

        return window.Echo;
    }).catch((error) => {
        echoLoader = null;

        throw error;
    });

    return echoLoader;
};


export { isDeepEqual, request };
