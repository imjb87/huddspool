
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

window.resultFormCollaboration = ({
    componentId,
    channelName,
    clientId,
    collaboratorId,
    collaboratorName,
    collaboratorColor,
}) => ({
    connectionHealth: 'healthy',
    connectionBadgeText: 'Live updates connected',
    connectionHeading: 'Live syncing is healthy',
    connectionMessage: '',
    connectionStateTimeoutId: null,
    foregroundSyncTimeoutId: null,
    hasBoundForegroundSync: false,
    hasConnectedOnce: false,
    collaboratorId: Number(collaboratorId ?? 0),
    collaboratorName: collaboratorName ?? 'Team admin',
    collaboratorColor: collaboratorColor ?? '#2563eb',
    fieldLockChannel: null,
    fieldLocks: {},
    activeFieldKey: null,
    fieldLockRenewalId: null,
    fieldLockExpiryTimers: {},
    fieldLockTtlMs: 12000,
    fieldLockRenewalMs: 4000,
    statusClassName(status, classes) {
        return classes[status] ?? classes.healthy;
    },
    applyConnectionState(connectionState) {
        if (this.connectionStateTimeoutId) {
            window.clearTimeout(this.connectionStateTimeoutId);
            this.connectionStateTimeoutId = null;
        }

        switch (connectionState) {
            case 'connected':
                this.connectionHealth = 'healthy';
                this.connectionBadgeText = 'Live updates connected';
                this.connectionHeading = 'Live syncing is healthy';
                this.connectionMessage = '';
                break;
            case 'connecting':
            case 'initialized':
            case 'unavailable':
                this.connectionHealth = 'weak';
                this.connectionBadgeText = 'Weak connection';
                this.connectionHeading = 'Weak connection detected';
                this.connectionMessage = 'Live updates may be delayed. It’s best if one person updates the result until your connection improves.';
                break;
            case 'disconnected':
            case 'failed':
            default:
                this.connectionHealth = 'lost';
                this.connectionBadgeText = 'Live updates disconnected';
                this.connectionHeading = 'Connection lost';
                this.connectionMessage = 'Live syncing is currently offline. Changes may be delayed or overwritten until the connection returns, so it’s best if one person updates the result for now.';
                break;
        }
    },
    updateConnectionState(state) {
        const connectionState = typeof state === 'string' ? state : state?.current;

        if (connectionState === 'connected') {
            this.hasConnectedOnce = true;
            this.applyConnectionState(connectionState);

            return;
        }

        if (! this.hasConnectedOnce && ['initialized', 'connecting'].includes(connectionState)) {
            return;
        }

        if (['disconnected', 'failed'].includes(connectionState)) {
            this.applyConnectionState(connectionState);

            return;
        }

        this.connectionStateTimeoutId = window.setTimeout(() => {
            this.applyConnectionState(connectionState);
        }, 1000);
    },
    echoConnection() {
        // Reverb currently uses Echo's Pusher-compatible connector, so the raw
        // connection state is read from the underlying Pusher connection here.
        return window.Echo?.connector?.pusher?.connection ?? null;
    },
    bindConnectionStatus() {
        const connection = this.echoConnection();

        if (!connection) {
            this.updateConnectionState('failed');

            return;
        }

        this.updateConnectionState(connection.state);
        connection.bind('state_change', (states) => this.updateConnectionState(states.current));
        connection.bind('error', () => this.updateConnectionState('failed'));
    },
    queueForegroundSync() {
        if (document.visibilityState === 'hidden') {
            return;
        }

        if (this.foregroundSyncTimeoutId) {
            window.clearTimeout(this.foregroundSyncTimeoutId);
        }

        this.foregroundSyncTimeoutId = window.setTimeout(() => {
            window.Livewire.find(componentId)?.call('refreshSharedDraft');
        }, 150);
    },
    bindForegroundSync() {
        if (this.hasBoundForegroundSync) {
            return;
        }

        this.hasBoundForegroundSync = true;

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.queueForegroundSync();
            }
        });

        window.addEventListener('pageshow', () => {
            this.queueForegroundSync();
        });

        window.addEventListener('focus', () => {
            this.queueForegroundSync();
        });
    },
    fieldLockPayload(action, lock) {
        return {
            action,
            field_key: lock.field_key,
            user_id: this.collaboratorId,
            user_name: this.collaboratorName,
            color: this.collaboratorColor,
            client_id: clientId,
            claimed_at: lock.claimed_at,
            expires_at: Date.now() + this.fieldLockTtlMs,
        };
    },
    whisperFieldLock(action, lock) {
        if (!this.fieldLockChannel || !lock) {
            return;
        }

        this.fieldLockChannel.whisper('result-field-lock', this.fieldLockPayload(action, lock));
    },
    requestResultFieldLockSync() {
        this.fieldLockChannel?.whisper('result-field-lock-sync-request', {
            requester_client_id: clientId,
        });
    },
    broadcastActiveResultFieldLock() {
        if (!this.activeFieldKey) {
            return;
        }

        const lock = this.fieldLocks[this.activeFieldKey];

        if (!lock || lock.client_id !== clientId) {
            return;
        }

        lock.expires_at = Date.now() + this.fieldLockTtlMs;
        this.fieldLocks[this.activeFieldKey] = lock;
        this.whisperFieldLock('renewed', lock);
        this.scheduleResultFieldLockExpiry(lock);
    },
    scheduleResultFieldLockExpiry(lock) {
        if (!lock?.field_key) {
            return;
        }

        if (this.fieldLockExpiryTimers[lock.field_key]) {
            window.clearTimeout(this.fieldLockExpiryTimers[lock.field_key]);
        }

        const delay = Math.max(Number(lock.expires_at ?? 0) - Date.now() + 1000, this.fieldLockTtlMs);

        this.fieldLockExpiryTimers[lock.field_key] = window.setTimeout(() => {
            const currentLock = this.fieldLocks[lock.field_key];

            if (!currentLock || currentLock.client_id !== lock.client_id || currentLock.claimed_at !== lock.claimed_at) {
                return;
            }

            delete this.fieldLocks[lock.field_key];

            if (this.activeFieldKey === lock.field_key) {
                this.activeFieldKey = null;
                this.stopResultFieldLockRenewal();
            }
        }, delay);
    },
    stopResultFieldLockRenewal() {
        if (this.fieldLockRenewalId) {
            window.clearInterval(this.fieldLockRenewalId);
            this.fieldLockRenewalId = null;
        }
    },
    startResultFieldLockRenewal() {
        this.stopResultFieldLockRenewal();

        this.fieldLockRenewalId = window.setInterval(() => {
            this.broadcastActiveResultFieldLock();
        }, this.fieldLockRenewalMs);
    },
    releaseResultFieldLock(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        if (!lock || lock.client_id !== clientId) {
            return;
        }

        this.whisperFieldLock('released', lock);
        delete this.fieldLocks[fieldKey];

        if (this.fieldLockExpiryTimers[fieldKey]) {
            window.clearTimeout(this.fieldLockExpiryTimers[fieldKey]);
            delete this.fieldLockExpiryTimers[fieldKey];
        }

        if (this.activeFieldKey === fieldKey) {
            this.activeFieldKey = null;
            this.stopResultFieldLockRenewal();
        }
    },
    beginResultFieldEditing(event) {
        const fieldKey = event.currentTarget?.dataset?.resultFieldKey;

        if (!fieldKey) {
            return;
        }

        const existingLock = this.fieldLocks[fieldKey];

        if (existingLock && existingLock.client_id !== clientId) {
            event.preventDefault();
            event.currentTarget.blur();

            return;
        }

        if (this.activeFieldKey && this.activeFieldKey !== fieldKey) {
            this.releaseResultFieldLock(this.activeFieldKey);
        }

        const lock = {
            field_key: fieldKey,
            client_id: clientId,
            claimed_at: existingLock?.claimed_at ?? Date.now(),
            color: this.collaboratorColor,
            user_id: this.collaboratorId,
            user_name: this.collaboratorName,
            expires_at: Date.now() + this.fieldLockTtlMs,
        };

        this.activeFieldKey = fieldKey;
        this.fieldLocks[fieldKey] = lock;
        this.whisperFieldLock(existingLock ? 'renewed' : 'acquired', lock);
        this.scheduleResultFieldLockExpiry(lock);
        this.startResultFieldLockRenewal();
    },
    endResultFieldEditing(event) {
        const fieldKey = event.currentTarget?.dataset?.resultFieldKey;

        if (!fieldKey) {
            return;
        }

        window.setTimeout(() => {
            if (document.activeElement?.dataset?.resultFieldKey === fieldKey) {
                return;
            }

            this.releaseResultFieldLock(fieldKey);
        }, 75);
    },
    cancelLocalResultFieldLock(fieldKey) {
        const activeElement = this.$el.querySelector(`[data-result-field-key="${fieldKey}"]`);

        if (activeElement instanceof HTMLElement) {
            activeElement.blur();
        }

        if (this.activeFieldKey === fieldKey) {
            this.activeFieldKey = null;
            this.stopResultFieldLockRenewal();
        }
    },
    incomingLockWins(currentLock, incomingLock) {
        if (!currentLock) {
            return true;
        }

        if (Number(incomingLock.claimed_at) !== Number(currentLock.claimed_at)) {
            return Number(incomingLock.claimed_at) < Number(currentLock.claimed_at);
        }

        return String(incomingLock.client_id) < String(currentLock.client_id);
    },
    receiveResultFieldLock(event = {}) {
        const fieldKey = event.field_key;

        if (!fieldKey || event.client_id === clientId) {
            return;
        }

        const currentLock = this.fieldLocks[fieldKey];

        if (event.action === 'released') {
            if (currentLock?.client_id !== event.client_id) {
                return;
            }

            delete this.fieldLocks[fieldKey];

            if (this.fieldLockExpiryTimers[fieldKey]) {
                window.clearTimeout(this.fieldLockExpiryTimers[fieldKey]);
                delete this.fieldLockExpiryTimers[fieldKey];
            }

            return;
        }

        const incomingLock = {
            field_key: fieldKey,
            client_id: String(event.client_id),
            claimed_at: Number(event.claimed_at ?? Date.now()),
            color: /^#[0-9a-f]{6}$/i.test(String(event.color ?? '')) ? event.color : '#64748b',
            user_id: Number(event.user_id ?? 0),
            user_name: event.user_name ?? 'Another editor',
            expires_at: Number(event.expires_at ?? Date.now() + this.fieldLockTtlMs),
        };

        if (currentLock && currentLock.client_id !== incomingLock.client_id && !this.incomingLockWins(currentLock, incomingLock)) {
            return;
        }

        if (currentLock?.client_id === clientId && incomingLock.client_id !== clientId) {
            this.cancelLocalResultFieldLock(fieldKey);
        }

        this.fieldLocks[fieldKey] = incomingLock;
        this.scheduleResultFieldLockExpiry(incomingLock);
    },
    releaseResultFieldLocksForCollaborator(userId) {
        const collaboratorId = Number(userId);

        Object.values(this.fieldLocks).forEach((lock) => {
            if (lock.user_id === collaboratorId && lock.client_id !== clientId) {
                delete this.fieldLocks[lock.field_key];

                if (this.fieldLockExpiryTimers[lock.field_key]) {
                    window.clearTimeout(this.fieldLockExpiryTimers[lock.field_key]);
                    delete this.fieldLockExpiryTimers[lock.field_key];
                }
            }
        });
    },
    resultFieldActivityStyle(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        if (!lock) {
            return '';
        }

        const borderRadius = fieldKey.endsWith('_player_id') ? '0.5rem' : '9999px';

        return `outline: 2px solid ${lock.color}aa; outline-offset: 2px; box-shadow: 0 0 12px 4px ${lock.color}55; border-radius: ${borderRadius};`;
    },
    collaboratorActivityStyle(collaborator) {
        const lock = Object.values(this.fieldLocks).find((fieldLock) => Number(fieldLock.user_id) === Number(collaborator.id));
        const color = lock?.color ?? collaborator.color ?? '#64748b';

        return `position: relative; z-index: 2; outline: 2px solid ${color}aa; outline-offset: 2px; box-shadow: 0 0 12px 4px ${color}55;`;
    },
    isResultFieldDisabled(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        return Boolean(lock && lock.client_id !== clientId);
    },
    resultFieldLockLabel(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        return lock && lock.client_id !== clientId ? `${lock.user_name} is editing this field` : '';
    },
    async init() {
        let echo = window.Echo ?? null;

        if (!echo) {
            try {
                echo = await window.ensureEcho?.();
            } catch (error) {
                this.updateConnectionState('failed');
                console.error('[result-collaboration] Failed to initialize Echo.', error);

                return;
            }
        }

        if (!echo || !window.Livewire) {
            this.updateConnectionState('failed');
            console.warn('[result-collaboration] Echo or Livewire is unavailable; collaboration channel was not initialized.', {
                channelName,
            });

            return;
        }

        this.bindConnectionStatus();
        this.bindForegroundSync();

        const syncUi = (members) => this.syncCollaboratorsUi?.(members);
        const joinUi = (member) => this.collaboratorJoinedUi?.(member);
        const leaveUi = (member) => {
            this.collaboratorLeftUi?.(member);
            this.releaseResultFieldLocksForCollaborator(member.id);
        };

        echo.leave(channelName);

        const channel = echo.join(channelName);
        this.fieldLockChannel = channel;

        channel
            .here((members) => {
                console.info('[result-collaboration] Connected to broadcast channel.', {
                    channelName,
                    members,
                });

                syncUi(members);
                window.setTimeout(() => this.requestResultFieldLockSync(), 200);
            })
            .joining((member) => {
                joinUi(member);
            })
            .leaving((member) => {
                leaveUi(member);
            })
            .listen('.league-result.draft-updated', (event) => {
                if (event.client_id === clientId) {
                    return;
                }

                window.Livewire.find(componentId)?.call('syncDraftFromBroadcast', event);
            })
            .listen('.league-result.submitted', (event) => {
                if (event.client_id === clientId || !event.result_url) {
                    return;
                }

                window.location.assign(event.result_url);
            });

        channel.listenForWhisper('result-field-lock', (event) => this.receiveResultFieldLock(event));
        channel.listenForWhisper('result-field-lock-sync-request', () => this.broadcastActiveResultFieldLock());
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

window.resultFormEditors = (initialCollaborators = [], colorPalette = []) => ({
    collaboratorColorPalette: Array.isArray(colorPalette) ? colorPalette : [],
    collaboratorColorAssignments: {},
    collaboratorsUi: [],
    initEditors() {
        this.collaboratorsUi = [];
        this.syncCollaboratorsUi(initialCollaborators);
    },
    syncCollaboratorsUi(members = []) {
        const normalizedMembers = this.normalizedCollaborators(members);
        const incomingIds = normalizedMembers.map((member) => member.id);

        this.collaboratorsUi.forEach((collaborator) => {
            if (!incomingIds.includes(collaborator.id)) {
                this.collaboratorLeftUi({ id: collaborator.id });
            }
        });

        normalizedMembers.forEach((member) => this.collaboratorJoinedUi(member, false));
        this.rebalanceCollaboratorColors(normalizedMembers);
    },
    normalizedCollaborators(members = []) {
        if (!Array.isArray(members)) {
            return [];
        }

        return [...new Map(
            members
                .map((member) => [Number(member.id), member])
                .filter(([collaboratorId]) => collaboratorId > 0),
        ).entries()]
            .map(([collaboratorId, member]) => ({ ...member, id: collaboratorId }))
            .sort((first, second) => first.id - second.id);
    },
    hslToHex(hue, saturation, lightness) {
        const normalizedHue = ((hue % 360) + 360) % 360;
        const saturationRatio = saturation / 100;
        const lightnessRatio = lightness / 100;
        const chroma = (1 - Math.abs(2 * lightnessRatio - 1)) * saturationRatio;
        const x = chroma * (1 - Math.abs((normalizedHue / 60) % 2 - 1));
        const match = lightnessRatio - chroma / 2;
        let rgb = [0, 0, 0];

        if (normalizedHue < 60) {
            rgb = [chroma, x, 0];
        } else if (normalizedHue < 120) {
            rgb = [x, chroma, 0];
        } else if (normalizedHue < 180) {
            rgb = [0, chroma, x];
        } else if (normalizedHue < 240) {
            rgb = [0, x, chroma];
        } else if (normalizedHue < 300) {
            rgb = [x, 0, chroma];
        } else {
            rgb = [chroma, 0, x];
        }

        return `#${rgb.map((channel) => Math.round((channel + match) * 255).toString(16).padStart(2, '0')).join('')}`;
    },
    generatedCollaboratorColor(index) {
        const hue = index * 137.508;
        const lightness = [48, 56, 64][index % 3];

        return this.hslToHex(hue, 72, lightness);
    },
    rebalanceCollaboratorColors(members = this.collaboratorsUi) {
        const normalizedMembers = this.normalizedCollaborators(members);
        const assignments = {};
        const usedColors = new Set();
        let generatedColorIndex = this.collaboratorColorPalette.length;

        normalizedMembers.forEach((member, rosterIndex) => {
            let color = this.collaboratorColorPalette[rosterIndex] ?? this.generatedCollaboratorColor(generatedColorIndex);

            while (usedColors.has(color)) {
                generatedColorIndex += 1;
                color = this.generatedCollaboratorColor(generatedColorIndex);
            }

            usedColors.add(color);
            assignments[member.id] = color;
        });

        this.collaboratorColorAssignments = assignments;

        this.collaboratorsUi.forEach((collaborator) => {
            if (assignments[collaborator.id]) {
                collaborator.color = assignments[collaborator.id];
            }
        });

        Object.values(this.fieldLocks ?? {}).forEach((lock) => {
            if (assignments[Number(lock.user_id)]) {
                lock.color = assignments[Number(lock.user_id)];
            }
        });

        if (assignments[this.collaboratorId]) {
            this.collaboratorColor = assignments[this.collaboratorId];
        }
    },
    collaboratorJoinedUi(member, rebalance = true) {
        const collaboratorId = Number(member.id);

        if (!collaboratorId) {
            return;
        }

        const existingCollaborator = this.collaboratorsUi.find((collaborator) => collaborator.id === collaboratorId);

        if (existingCollaborator) {
            existingCollaborator.name = member.name ?? existingCollaborator.name;
            existingCollaborator.avatar_url = member.avatar_url ?? existingCollaborator.avatar_url;
            existingCollaborator.color = member.color ?? existingCollaborator.color;
            existingCollaborator.isVisible = true;

            if (rebalance) {
                this.rebalanceCollaboratorColors();
            }

            return;
        }

        this.collaboratorsUi.push({
            id: collaboratorId,
            name: member.name ?? 'Team admin',
            avatar_url: member.avatar_url ?? '/images/user.jpg',
            color: member.color ?? '#64748b',
            isVisible: false,
        });

        this.$nextTick(() => {
            const collaborator = this.collaboratorsUi.find((entry) => entry.id === collaboratorId);

            if (collaborator) {
                collaborator.isVisible = true;
            }
        });

        if (rebalance) {
            this.rebalanceCollaboratorColors();
        }
    },
    collaboratorLeftUi(member) {
        const collaboratorId = Number(member.id);
        const collaborator = this.collaboratorsUi.find((entry) => entry.id === collaboratorId);

        if (!collaborator) {
            return;
        }

        collaborator.isVisible = false;
        this.rebalanceCollaboratorColors(this.collaboratorsUi.filter((entry) => entry.id !== collaboratorId));

        window.setTimeout(() => {
            this.collaboratorsUi = this.collaboratorsUi.filter((entry) => entry.id !== collaboratorId);
            this.rebalanceCollaboratorColors();
        }, 220);
    },
});

window.resultFormRecovery = ({ componentId, fixtureId, draftVersion, isLocked }) => ({
    currentDraftVersion: Number(draftVersion ?? 0),
    storageKey: `result-form-recovery:${fixtureId}`,
    saveTimeoutId: null,
    initRecovery() {
        if (isLocked) {
            this.clearSavedDraft();

            return;
        }

        this.restoreSavedDraft();
        this.persistSavedDraft();

        this.$el.addEventListener('change', () => this.queueSavedDraft(), true);
        this.$el.addEventListener('input', () => this.queueSavedDraft(), true);
    },
    queueSavedDraft() {
        if (this.saveTimeoutId) {
            window.clearTimeout(this.saveTimeoutId);
        }

        this.saveTimeoutId = window.setTimeout(() => {
            this.persistSavedDraft();
        }, 75);
    },
    persistSavedDraft() {
        const frames = this.readFramesFromDom();
        const existingPayload = this.readSavedDraft();
        const baseFrames = existingPayload && Number(existingPayload.draftVersion ?? -1) === this.currentDraftVersion
            ? this.normalizeFrames(existingPayload.baseFrames ?? existingPayload.frames ?? frames)
            : this.normalizeFrames(frames);

        window.localStorage.setItem(this.storageKey, JSON.stringify({
            draftVersion: this.currentDraftVersion,
            baseFrames,
            frames: this.normalizeFrames(frames),
        }));
    },
    restoreSavedDraft() {
        const payload = this.readSavedDraft();

        if (!payload || Number(payload.draftVersion ?? -1) !== this.currentDraftVersion) {
            return;
        }

        const savedFrames = this.normalizeFrames(payload.frames ?? {});

        if (isDeepEqual(savedFrames, this.readFramesFromDom())) {
            return;
        }

        window.Livewire.find(componentId)?.call('restoreClientDraft', savedFrames, this.currentDraftVersion);
    },
    syncSavedDraft(detail = {}) {
        const nextDraftVersion = Number(detail.draftVersion ?? this.currentDraftVersion ?? 0);
        const latestFrames = this.normalizeFrames(detail.frames ?? this.readFramesFromDom());
        const existingPayload = this.readSavedDraft();

        if (detail.isLocked) {
            this.clearSavedDraft();

            return;
        }

        if (existingPayload && Number(existingPayload.draftVersion ?? -1) < nextDraftVersion) {
            const mergedFrames = this.mergeFrames(
                this.normalizeFrames(existingPayload.baseFrames ?? {}),
                this.normalizeFrames(existingPayload.frames ?? {}),
                latestFrames,
            );

            if (!isDeepEqual(mergedFrames, latestFrames)) {
                this.currentDraftVersion = nextDraftVersion;

                window.localStorage.setItem(this.storageKey, JSON.stringify({
                    draftVersion: this.currentDraftVersion,
                    baseFrames: latestFrames,
                    frames: mergedFrames,
                }));

                window.Livewire.find(componentId)?.call('mergeClientDraft', mergedFrames, this.currentDraftVersion);

                return;
            }
        }

        this.currentDraftVersion = nextDraftVersion;

        window.localStorage.setItem(this.storageKey, JSON.stringify({
            draftVersion: this.currentDraftVersion,
            baseFrames: latestFrames,
            frames: latestFrames,
        }));
    },
    clearSavedDraft() {
        window.localStorage.removeItem(this.storageKey);
    },
    readSavedDraft() {
        const rawPayload = window.localStorage.getItem(this.storageKey);

        if (!rawPayload) {
            return null;
        }

        try {
            return JSON.parse(rawPayload);
        } catch (error) {
            this.clearSavedDraft();

            return null;
        }
    },
    readFramesFromDom() {
        return Array.from(this.$el.querySelectorAll('[data-result-frame-field]')).reduce((frames, field) => {
            const frameNumber = Number(field.dataset.frameNumber);
            const side = field.dataset.frameSide;
            const valueType = field.dataset.frameValue;

            if (!frameNumber || !side || !valueType) {
                return frames;
            }

            if (!frames[frameNumber]) {
                frames[frameNumber] = {
                    home_player_id: null,
                    away_player_id: null,
                    home_score: 0,
                    away_score: 0,
                };
            }

            const frame = frames[frameNumber];
            const value = field.value === '' ? null : field.value;

            if (side === 'home' && valueType === 'player') {
                frame.home_player_id = value;
            } else if (side === 'away' && valueType === 'player') {
                frame.away_player_id = value;
            } else if (side === 'home' && valueType === 'score') {
                frame.home_score = Number(value ?? 0);
            } else if (side === 'away' && valueType === 'score') {
                frame.away_score = Number(value ?? 0);
            }

            return frames;
        }, {});
    },
    normalizeFrames(frames = {}) {
        return Array.from({ length: 10 }, (_, index) => index + 1).reduce((normalizedFrames, frameNumber) => {
            const frame = frames[frameNumber] ?? frames[String(frameNumber)] ?? {};

            normalizedFrames[frameNumber] = {
                home_player_id: frame.home_player_id === '' ? null : frame.home_player_id ?? null,
                away_player_id: frame.away_player_id === '' ? null : frame.away_player_id ?? null,
                home_score: Number(frame.home_score ?? 0),
                away_score: Number(frame.away_score ?? 0),
            };

            return normalizedFrames;
        }, {});
    },
    mergeFrames(baseFrames, localFrames, latestFrames) {
        return Array.from({ length: 10 }, (_, index) => index + 1).reduce((mergedFrames, frameNumber) => {
            const mergedFrame = { ...latestFrames[frameNumber] };
            const baseFrame = baseFrames[frameNumber] ?? {};
            const localFrame = localFrames[frameNumber] ?? {};

            ['home_player_id', 'away_player_id', 'home_score', 'away_score'].forEach((field) => {
                if (isDeepEqual(localFrame[field], baseFrame[field])) {
                    return;
                }

                if (isDeepEqual(latestFrames[frameNumber]?.[field], baseFrame[field])) {
                    mergedFrame[field] = localFrame[field];
                }
            });

            mergedFrames[frameNumber] = mergedFrame;

            return mergedFrames;
        }, {});
    },
});

window.resultFormPresenceTooltip = (fieldKey = null) => ({
    fieldKey,
    open: false,
    isPositioned: false,
    tooltipStyle: '',
    tooltipFrameId: null,
    tooltipViewportHandler: null,
    init() {
        this.tooltipViewportHandler = () => {
            if (this.open) {
                this.scheduleTooltipPosition();
            }
        };

        window.addEventListener('resize', this.tooltipViewportHandler);
        window.addEventListener('scroll', this.tooltipViewportHandler, true);
    },
    destroy() {
        this.cancelTooltipFrame();

        if (this.tooltipViewportHandler) {
            window.removeEventListener('resize', this.tooltipViewportHandler);
            window.removeEventListener('scroll', this.tooltipViewportHandler, true);
        }
    },
    isLockedField() {
        return Boolean(
            this.fieldKey
            && this.isResultFieldDisabled
            && this.isResultFieldDisabled(this.fieldKey),
        );
    },
    tooltipLock() {
        return this.isLockedField() ? this.fieldLocks?.[this.fieldKey] : null;
    },
    tooltipLabel() {
        return this.tooltipLock()?.user_name ?? '';
    },
    tooltipColor() {
        return this.tooltipLock()?.color ?? '';
    },
    tooltipTextColor(color) {
        const hex = String(color ?? '').replace('#', '');

        if (!/^[0-9a-f]{6}$/i.test(hex)) {
            return '#ffffff';
        }

        const red = Number.parseInt(hex.slice(0, 2), 16);
        const green = Number.parseInt(hex.slice(2, 4), 16);
        const blue = Number.parseInt(hex.slice(4, 6), 16);
        const luminance = ((red * 299) + (green * 587) + (blue * 114)) / 1000;

        return luminance > 165 ? '#111827' : '#ffffff';
    },
    tooltipColorStyle(color) {
        if (!color) {
            return '';
        }

        return `background-color:${color};color:${this.tooltipTextColor(color)};`;
    },
    showTooltip() {
        if (this.fieldKey && !this.isLockedField()) {
            return;
        }

        this.open = true;
        this.isPositioned = false;

        this.$nextTick(() => {
            this.scheduleTooltipPosition();
        });
    },
    hideTooltip() {
        this.cancelTooltipFrame();
        this.open = false;
        this.isPositioned = false;
        this.tooltipStyle = '';
    },
    cancelTooltipFrame() {
        if (this.tooltipFrameId) {
            window.cancelAnimationFrame(this.tooltipFrameId);
            this.tooltipFrameId = null;
        }
    },
    measureTooltipPosition() {
        if (!this.$refs.trigger || !this.$refs.tooltip) {
            return null;
        }

        const viewportPadding = 8;
        const tooltipGap = 8;
        const triggerBounds = this.$refs.trigger.getBoundingClientRect();
        const tooltipWidth = this.$refs.tooltip.offsetWidth;
        const tooltipHeight = this.$refs.tooltip.offsetHeight;
        const centeredLeft = triggerBounds.left + (triggerBounds.width / 2) - (tooltipWidth / 2);
        const clampedLeft = Math.max(
            viewportPadding,
            Math.min(window.innerWidth - viewportPadding - tooltipWidth, centeredLeft),
        );
        const aboveTop = triggerBounds.top - tooltipHeight - tooltipGap;
        const belowTop = triggerBounds.bottom + tooltipGap;
        const maxTop = Math.max(viewportPadding, window.innerHeight - viewportPadding - tooltipHeight);
        const top = aboveTop >= viewportPadding
            ? aboveTop
            : belowTop + tooltipHeight <= window.innerHeight - viewportPadding
                ? belowTop
                : Math.min(Math.max(belowTop, viewportPadding), maxTop);

        return {
            left: clampedLeft,
            top,
        };
    },
    scheduleTooltipPosition() {
        this.cancelTooltipFrame();

        this.tooltipFrameId = window.requestAnimationFrame(() => {
            const position = this.measureTooltipPosition();

            this.tooltipFrameId = null;

            if (!position) {
                return;
            }

            this.tooltipStyle = `left:${position.left}px;top:${position.top}px;`;
            this.isPositioned = true;
        });
    },
});
