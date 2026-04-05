
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

window.resultFormCollaboration = ({ componentId, channelName, clientId }) => ({
    connectionHealth: 'healthy',
    connectionBadgeText: 'Live updates connected',
    connectionHeading: 'Live syncing is healthy',
    connectionMessage: '',
    connectionStateTimeoutId: null,
    foregroundSyncTimeoutId: null,
    hasBoundForegroundSync: false,
    hasConnectedOnce: false,
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
        const leaveUi = (member) => this.collaboratorLeftUi?.(member);

        echo.leave(channelName);

        echo.join(channelName)
            .here((members) => {
                console.info('[result-collaboration] Connected to broadcast channel.', {
                    channelName,
                    members,
                });

                syncUi(members);
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

window.registerHeaderNotificationsStore = (Alpine) => {
    Alpine.store('headerNotifications', {
        initialized: false,
        loading: false,
        unreadCount: 0,
        notifications: [],
        summaryUrl: null,
        readAllUrl: null,
        readUrlTemplate: null,
        configure({ summaryUrl, readAllUrl, readUrlTemplate }) {
            this.summaryUrl = summaryUrl;
            this.readAllUrl = readAllUrl;
            this.readUrlTemplate = readUrlTemplate;

            if (! this.initialized) {
                this.refresh();
            }
        },
        async refresh() {
            if (this.loading || ! this.summaryUrl) {
                return;
            }

            this.loading = true;

            try {
                const response = await request(this.summaryUrl);
                const payload = await response.json();
                this.applyPayload(payload);
                this.initialized = true;
            } catch (error) {
                console.error('[header-notifications] Failed to refresh notification summary.', error);
            } finally {
                this.loading = false;
            }
        },
        async markAllAsRead() {
            if (! this.readAllUrl) {
                return;
            }

            try {
                const response = await request(this.readAllUrl, {
                    method: 'POST',
                });
                this.applyPayload(await response.json());
            } catch (error) {
                console.error('[header-notifications] Failed to mark all notifications as read.', error);
            }
        },
        async markAsRead(notificationId) {
            if (! this.readUrlTemplate || ! notificationId) {
                return;
            }

            try {
                const response = await request(
                    this.readUrlTemplate.replace('__NOTIFICATION__', notificationId),
                    {
                        method: 'POST',
                    },
                );

                this.applyPayload(await response.json());
            } catch (error) {
                console.error('[header-notifications] Failed to mark notification as read.', error);
            }
        },
        applyPayload(payload) {
            this.unreadCount = Number(payload?.unread_count ?? 0);
            this.notifications = Array.isArray(payload?.notifications) ? payload.notifications : [];
        },
    });
};

window.resultFormEditors = (initialCollaborators = []) => ({
    collaboratorsUi: [],
    initEditors() {
        this.collaboratorsUi = [];
        this.syncCollaboratorsUi(initialCollaborators);
    },
    syncCollaboratorsUi(members = []) {
        const incomingIds = members.map((member) => Number(member.id));

        this.collaboratorsUi.forEach((collaborator) => {
            if (!incomingIds.includes(collaborator.id)) {
                this.collaboratorLeftUi({ id: collaborator.id });
            }
        });

        members.forEach((member) => this.collaboratorJoinedUi(member));
    },
    collaboratorJoinedUi(member) {
        const collaboratorId = Number(member.id);

        if (!collaboratorId) {
            return;
        }

        const existingCollaborator = this.collaboratorsUi.find((collaborator) => collaborator.id === collaboratorId);

        if (existingCollaborator) {
            existingCollaborator.name = member.name ?? existingCollaborator.name;
            existingCollaborator.avatar_url = member.avatar_url ?? existingCollaborator.avatar_url;
            existingCollaborator.isVisible = true;

            return;
        }

        this.collaboratorsUi.push({
            id: collaboratorId,
            name: member.name ?? 'Team admin',
            avatar_url: member.avatar_url ?? '/images/user.jpg',
            isVisible: false,
        });

        this.$nextTick(() => {
            const collaborator = this.collaboratorsUi.find((entry) => entry.id === collaboratorId);

            if (collaborator) {
                collaborator.isVisible = true;
            }
        });
    },
    collaboratorLeftUi(member) {
        const collaboratorId = Number(member.id);
        const collaborator = this.collaboratorsUi.find((entry) => entry.id === collaboratorId);

        if (!collaborator) {
            return;
        }

        collaborator.isVisible = false;

        window.setTimeout(() => {
            this.collaboratorsUi = this.collaboratorsUi.filter((entry) => entry.id !== collaboratorId);
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

window.resultFormPresenceTooltip = () => ({
    open: false,
    isPositioned: false,
    tooltipStyle: '',
    tooltipFrameId: null,
    showTooltip() {
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
        const triggerBounds = this.$refs.trigger.getBoundingClientRect();
        const tooltipWidth = this.$refs.tooltip.offsetWidth;
        const centeredLeft = triggerBounds.left + (triggerBounds.width / 2);
        const clampedLeft = Math.max(
            viewportPadding + (tooltipWidth / 2),
            Math.min(window.innerWidth - viewportPadding - (tooltipWidth / 2), centeredLeft),
        );

        return {
            left: clampedLeft,
            top: triggerBounds.top - 8,
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
