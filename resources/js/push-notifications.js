import { request } from './bootstrap';

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const normalized = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(normalized);

    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
};

const detectPushDeviceMetadata = () => {
    const userAgent = navigator.userAgent ?? '';
    const platform = (() => {
        if (/iPhone/i.test(userAgent)) {
            return 'iPhone';
        }

        if (/iPad/i.test(userAgent)) {
            return 'iPad';
        }

        if (/Android/i.test(userAgent)) {
            return 'Android';
        }

        if (/Mac OS X|Macintosh/i.test(userAgent)) {
            return 'macOS';
        }

        if (/Windows/i.test(userAgent)) {
            return 'Windows';
        }

        if (/Linux/i.test(userAgent)) {
            return 'Linux';
        }

        return 'Unknown platform';
    })();

    const browser = (() => {
        if (/Edg\//i.test(userAgent)) {
            return 'Edge';
        }

        if (/CriOS/i.test(userAgent)) {
            return 'Chrome';
        }

        if (/Chrome\//i.test(userAgent) && !/Edg\//i.test(userAgent)) {
            return 'Chrome';
        }

        if (/Firefox\//i.test(userAgent)) {
            return 'Firefox';
        }

        if (/Safari\//i.test(userAgent) && !/Chrome\//i.test(userAgent) && !/CriOS/i.test(userAgent)) {
            return 'Safari';
        }

        return 'Unknown browser';
    })();

    return {
        device_label: `${browser} on ${platform}`,
        browser,
        platform,
        user_agent: userAgent,
    };
};

window.pushNotificationsPanel = ({ configured, enabled, publicKey, subscribeUrl, unsubscribeUrl }) => ({
    configured,
    enabled,
    supported: false,
    busy: false,
    permission: typeof window.Notification === 'undefined' ? 'unsupported' : window.Notification.permission,
    error: '',
    async init() {
        this.supported = this.configured
            && 'Notification' in window
            && 'serviceWorker' in navigator
            && 'PushManager' in window;

        if (!this.supported) {
            return;
        }

        const registration = await navigator.serviceWorker.ready;
        const existingSubscription = await registration.pushManager.getSubscription();

        this.enabled = Boolean(existingSubscription) || this.enabled;
        this.permission = window.Notification.permission;
    },
    async enable() {
        if (this.busy || !this.supported) {
            return;
        }

        this.busy = true;
        this.error = '';

        try {
            let permission = window.Notification.permission;

            if (permission === 'default') {
                permission = await window.Notification.requestPermission();
            }

            this.permission = permission;

            if (permission !== 'granted') {
                this.error = 'Browser notifications are currently blocked for this device.';

                return;
            }

            const registration = await navigator.serviceWorker.ready;

            let subscription = await registration.pushManager.getSubscription();

            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(publicKey),
                });
            }

            const payload = subscription.toJSON();

            await request(subscribeUrl, {
                method: 'POST',
                body: {
                    endpoint: payload.endpoint,
                    public_key: payload.keys?.p256dh,
                    auth_token: payload.keys?.auth,
                    content_encoding: payload.contentEncoding ?? 'aes128gcm',
                    ...detectPushDeviceMetadata(),
                },
            });

            this.enabled = true;
        } catch (error) {
            this.error = 'We could not enable browser notifications just now.';
            console.error('[push-notifications] Failed to subscribe.', error);
        } finally {
            this.busy = false;
        }
    },
    async disable() {
        if (this.busy || !this.supported) {
            return;
        }

        this.busy = true;
        this.error = '';

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();

            if (subscription) {
                await request(unsubscribeUrl, {
                    method: 'DELETE',
                    body: {
                        endpoint: subscription.endpoint,
                    },
                });

                await subscription.unsubscribe();
            }

            this.enabled = false;
        } catch (error) {
            this.error = 'We could not disable browser notifications just now.';
            console.error('[push-notifications] Failed to unsubscribe.', error);
        } finally {
            this.busy = false;
        }
    },
});
window.nativePushPermissionPrompt = ({ publicKey, subscribeUrl, acknowledgeUrl }) => ({
    acknowledged: false,
    async init() {
        const supported = 'Notification' in window
            && 'serviceWorker' in navigator
            && 'PushManager' in window;

        if (!supported) {
            await this.acknowledge();

            return;
        }

        let permission = window.Notification.permission;

        if (permission === 'denied') {
            await this.acknowledge();

            return;
        }

        if (permission === 'default') {
            permission = await window.Notification.requestPermission();
        }

        if (permission !== 'granted') {
            await this.acknowledge();

            return;
        }

        try {
            const registration = await navigator.serviceWorker.ready;
            let subscription = await registration.pushManager.getSubscription();

            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(publicKey),
                });
            }

            const payload = subscription.toJSON();

            await request(subscribeUrl, {
                method: 'POST',
                body: {
                    endpoint: payload.endpoint,
                    public_key: payload.keys?.p256dh,
                    auth_token: payload.keys?.auth,
                    content_encoding: payload.contentEncoding ?? 'aes128gcm',
                    ...detectPushDeviceMetadata(),
                },
            });
        } catch (error) {
            console.error('[push-notifications] Failed to complete the one-time native permission prompt flow.', error);

            await this.acknowledge();
        }
    },
    async acknowledge() {
        if (this.acknowledged) {
            return;
        }

        this.acknowledged = true;

        try {
            await request(acknowledgeUrl, {
                method: 'POST',
            });
        } catch (error) {
        console.error('[push-notifications] Failed to acknowledge the one-time native permission prompt.', error);
        }
    },
});
