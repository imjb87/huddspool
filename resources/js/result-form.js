import { isDeepEqual } from './bootstrap';

const resultFormCollaboration = ({
    componentId,
    channelName,
    clientId,
    collaboratorId,
    collaboratorName,
    collaboratorColor,
    formLocked,
    formCanEdit,
}) => ({
    connectionHealth: 'healthy',
    connectionBadgeText: 'Live updates connected',
    connectionHeading: 'Live syncing is healthy',
    connectionMessage: '',
    connectionStateTimeoutId: null,
    foregroundSyncTimeoutId: null,
    connectionCleanup: null,
    foregroundSyncCleanup: null,
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
    fieldLockDomSyncFrameId: null,
    fieldLockDomObserver: null,
    livewireFieldLockHookCleanup: null,
    hasBoundFieldLockDomSync: false,
    formLocked: Boolean(formLocked),
    formCanEdit: formCanEdit !== false,
    fieldLockTtlMs: 12000,
    fieldLockRenewalMs: 4000,
    fieldLockBlurGraceMs: 250,
    fieldLockBlurTimeouts: {},
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

        if (this.connectionStateTimeoutId) {
            window.clearTimeout(this.connectionStateTimeoutId);
            this.connectionStateTimeoutId = null;
        }

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

        this.connectionCleanup?.();

        const handleStateChange = (states) => this.updateConnectionState(states.current);
        const handleError = () => this.updateConnectionState('failed');

        this.updateConnectionState(connection.state);
        connection.bind('state_change', handleStateChange);
        connection.bind('error', handleError);

        this.connectionCleanup = () => {
            connection.unbind?.('state_change', handleStateChange);
            connection.unbind?.('error', handleError);
        };
    },
    queueForegroundSync() {
        if (document.visibilityState === 'hidden') {
            return;
        }

        if (this.foregroundSyncTimeoutId) {
            window.clearTimeout(this.foregroundSyncTimeoutId);
        }

        this.foregroundSyncTimeoutId = window.setTimeout(() => {
            this.foregroundSyncTimeoutId = null;
            window.Livewire.find(componentId)?.call('refreshSharedDraft');
        }, 150);
    },
    bindForegroundSync() {
        if (this.hasBoundForegroundSync) {
            return;
        }

        this.hasBoundForegroundSync = true;

        const handleVisibilityChange = () => {
            if (document.visibilityState === 'visible') {
                this.queueForegroundSync();
            }
        };
        const handlePageShow = () => this.queueForegroundSync();
        const handleFocus = () => this.queueForegroundSync();

        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('pageshow', handlePageShow);
        window.addEventListener('focus', handleFocus);

        this.foregroundSyncCleanup = () => {
            document.removeEventListener('visibilitychange', handleVisibilityChange);
            window.removeEventListener('pageshow', handlePageShow);
            window.removeEventListener('focus', handleFocus);
        };
    },
    syncServerResultState(detail = {}) {
        const payload = Array.isArray(detail) ? (detail[0] ?? {}) : detail;

        if (Object.prototype.hasOwnProperty.call(payload, 'isLocked')) {
            this.formLocked = Boolean(payload.isLocked);
        }

        if (Object.prototype.hasOwnProperty.call(payload, 'canEdit')) {
            this.formCanEdit = Boolean(payload.canEdit);
        }

        this.syncPlayerPickerSelections();
        this.queueFieldLockDomSync();
    },
    queueFieldLockDomSync() {
        if (this.fieldLockDomSyncFrameId) {
            return;
        }

        const schedule = window.requestAnimationFrame
            ? (callback) => window.requestAnimationFrame(callback)
            : (callback) => window.setTimeout(callback, 0);

        this.fieldLockDomSyncFrameId = schedule(() => {
            this.fieldLockDomSyncFrameId = null;
            this.syncResultFieldLockDom();
        });
    },
    syncResultFieldLockDom() {
        if (!this.$el?.querySelectorAll) {
            return;
        }

        this.$el.querySelectorAll('[data-result-field-group]').forEach((group) => {
            const fieldKey = group.dataset.resultFieldKey;
            const lock = fieldKey ? this.fieldLocks[fieldKey] : null;
            const remoteLock = Boolean(lock && lock.client_id !== clientId);
            const serverDisabled = this.formLocked || !this.formCanEdit;
            const fieldDisabled = remoteLock || serverDisabled;

            const nextStyle = fieldKey ? this.resultFieldActivityStyle(fieldKey) : '';

            if ((group.getAttribute('style') ?? '') !== nextStyle) {
                if (nextStyle) {
                    group.setAttribute('style', nextStyle);
                } else {
                    group.removeAttribute('style');
                }
            }

            if (lock) {
                group.dataset.resultFieldLocked = 'true';
                group.setAttribute('aria-label', lock.user_name + ' is editing this field');
            } else {
                delete group.dataset.resultFieldLocked;
                group.removeAttribute('aria-label');
            }

            if (fieldDisabled) {
                group.dataset.resultFieldDisabled = 'true';
                group.setAttribute('aria-disabled', 'true');
            } else {
                delete group.dataset.resultFieldDisabled;
                group.removeAttribute('aria-disabled');
            }
        });

        this.$el.querySelectorAll('[data-result-field-control][data-result-field-key]').forEach((field) => {
            const fieldKey = field.dataset.resultFieldKey;
            const lock = fieldKey ? this.fieldLocks[fieldKey] : null;
            const remoteLock = Boolean(lock && lock.client_id !== clientId);
            const serverDisabled = this.formLocked || !this.formCanEdit;
            const fieldDisabled = remoteLock || serverDisabled;
            const isLabel = field.tagName === 'LABEL';

            if (fieldDisabled) {
                if (!isLabel && !field.disabled) {
                    field.setAttribute('disabled', 'disabled');
                }
                field.dataset.resultFieldDisabled = 'true';
                field.setAttribute('aria-disabled', 'true');
            } else if (field.dataset.resultFieldDisabled === 'true') {
                if (!isLabel && field.dataset.resultFieldLockDisabled === 'true') {
                    field.removeAttribute('disabled');
                }

                delete field.dataset.resultFieldDisabled;
                field.removeAttribute('aria-disabled');
            }

            if (fieldDisabled && !isLabel) {
                field.dataset.resultFieldLockDisabled = 'true';
            } else if (!fieldDisabled && field.dataset.resultFieldLockDisabled === 'true') {
                field.removeAttribute('disabled');
                delete field.dataset.resultFieldLockDisabled;
            }

            if (remoteLock) {
                field.dataset.resultFieldLockDisabled = 'true';
                const title = lock.user_name + ' is editing this field';

                if (field.getAttribute('title') !== title) {
                    field.setAttribute('title', title);
                }
            } else {
                delete field.dataset.resultFieldLockDisabled;

                if (! serverDisabled) {
                    field.removeAttribute('title');
                }
            }
        });
    },
    bindFieldLockDomSync() {
        if (this.hasBoundFieldLockDomSync || !this.$el) {
            return;
        }

        this.hasBoundFieldLockDomSync = true;
        this.queueFieldLockDomSync();

        if (typeof MutationObserver !== 'undefined') {
            this.fieldLockDomObserver = new MutationObserver((mutations) => {
                if (mutations.some((mutation) => mutation.type === 'childList' || mutation.type === 'attributes')) {
                    this.queueFieldLockDomSync();
                }
            });

            this.fieldLockDomObserver.observe(this.$el, {
                attributes: true,
                attributeFilter: ['disabled', 'style', 'title'],
                childList: true,
                subtree: true,
            });
        }

        if (window.Livewire?.hook) {
            this.livewireFieldLockHookCleanup = window.Livewire.hook('morph.updated', ({ component }) => {
                if (component?.id === componentId) {
                    this.syncPlayerPickerSelections();
                    this.queueFieldLockDomSync();
                }
            });
        }
    },
    fieldLockPayload(action, lock) {
        return {
            action,
            field_key: lock.field_key,
            user_id: this.collaboratorId,
            user_name: this.collaboratorName,
            color: this.collaboratorColor,
            client_id: clientId,
            lock_id: lock.lock_id,
            claimed_at: lock.claimed_at,
            sent_at: Date.now(),
            ttl_ms: this.fieldLockTtlMs,
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
        this.queueFieldLockDomSync();
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

            if (!this.isSameResultFieldLock(currentLock, lock)) {
                return;
            }

            delete this.fieldLocks[lock.field_key];

            if (this.activeFieldKey === lock.field_key) {
                this.activeFieldKey = null;
                this.stopResultFieldLockRenewal();
            }

            this.queueFieldLockDomSync();
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

        this.queueFieldLockDomSync();
    },
    createResultFieldLockId() {
        if (globalThis.crypto?.randomUUID) {
            return globalThis.crypto.randomUUID();
        }

        return [clientId, Date.now(), Math.random().toString(36).slice(2)].join(':');
    },
    touchResultFieldLock(event) {
        const fieldKey = event.currentTarget?.dataset?.resultFieldKey;

        if (!fieldKey || this.activeFieldKey !== fieldKey) {
            return;
        }

        const lock = this.fieldLocks[fieldKey];

        if (!lock || lock.client_id !== clientId) {
            return;
        }

        lock.expires_at = Date.now() + this.fieldLockTtlMs;
        this.fieldLocks[fieldKey] = lock;
        this.whisperFieldLock('renewed', lock);
        this.scheduleResultFieldLockExpiry(lock);
        this.queueFieldLockDomSync();
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
            lock_id: existingLock?.lock_id ?? this.createResultFieldLockId(),
            claimed_at: Number(existingLock?.claimed_at ?? Date.now()),
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
        this.queueFieldLockDomSync();
    },
    endResultFieldEditing(event) {
        const fieldKey = event.currentTarget?.dataset?.resultFieldKey;

        if (!fieldKey) {
            return;
        }

        const timeoutId = window.setTimeout(() => {
            delete this.fieldLockBlurTimeouts[timeoutId];
            const activeFieldKey = document.activeElement?.closest?.('[data-result-field-key]')?.dataset?.resultFieldKey
                ?? document.activeElement?.dataset?.resultFieldKey;

            if (activeFieldKey === fieldKey || this.activeFieldKey !== fieldKey) {
                return;
            }

            this.releaseResultFieldLock(fieldKey);
        }, this.fieldLockBlurGraceMs);
        this.fieldLockBlurTimeouts[timeoutId] = true;
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

        this.queueFieldLockDomSync();
    },
    isSameResultFieldLock(left, right) {
        if (!left || !right || left.client_id !== right.client_id) {
            return false;
        }

        if (left.lock_id && right.lock_id) {
            return left.lock_id === right.lock_id;
        }

        return Number(left.claimed_at) === Number(right.claimed_at);
    },
    incomingLockWins(currentLock, incomingLock) {
        if (!currentLock) {
            return true;
        }

        if (String(incomingLock.client_id) === String(currentLock.client_id)) {
            return Number(incomingLock.claimed_at) >= Number(currentLock.claimed_at);
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
            if (
                !currentLock
                || currentLock.client_id !== String(event.client_id)
                || (currentLock.lock_id && !event.lock_id)
                || (event.lock_id && currentLock.lock_id !== String(event.lock_id))
            ) {
                return;
            }

            delete this.fieldLocks[fieldKey];

            if (this.fieldLockExpiryTimers[fieldKey]) {
                window.clearTimeout(this.fieldLockExpiryTimers[fieldKey]);
                delete this.fieldLockExpiryTimers[fieldKey];
            }

            this.queueFieldLockDomSync();

            return;
        }

        const expiresAt = Number(event.expires_at ?? Date.now() + this.fieldLockTtlMs);

        if (expiresAt <= Date.now()) {
            return;
        }

        const incomingLock = {
            field_key: fieldKey,
            client_id: String(event.client_id),
            lock_id: String(event.lock_id ?? [event.client_id, event.claimed_at ?? Date.now()].join(':')),
            claimed_at: Number(event.claimed_at ?? Date.now()),
            color: this.collaboratorColorAssignments?.[Number(event.user_id)]
                ?? (/^#[0-9a-f]{6}$/i.test(String(event.color ?? '')) ? event.color : '#64748b'),
            user_id: Number(event.user_id ?? 0),
            user_name: event.user_name ?? 'Another editor',
            expires_at: expiresAt,
        };

        if (currentLock && !this.incomingLockWins(currentLock, incomingLock)) {
            return;
        }

        if (currentLock?.client_id === clientId && incomingLock.client_id !== clientId) {
            this.cancelLocalResultFieldLock(fieldKey);
        }

        this.fieldLocks[fieldKey] = incomingLock;
        this.scheduleResultFieldLockExpiry(incomingLock);
        this.queueFieldLockDomSync();
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

        this.queueFieldLockDomSync();
    },
    resultFieldActivityStyle(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        if (!lock) {
            return '';
        }

        const borderRadius = fieldKey.endsWith('_player_id') ? '0.5rem' : '9999px';

        const color = this.collaboratorColorAssignments?.[Number(lock.user_id)] ?? lock.color;

        return `outline: 2px solid ${color}aa; outline-offset: 2px; box-shadow: 0 0 12px 4px ${color}55; border-radius: ${borderRadius};`;
    },
    collaboratorActivityStyle(collaborator) {
        const lock = Object.values(this.fieldLocks).find((fieldLock) => Number(fieldLock.user_id) === Number(collaborator.id));
        const color = this.collaboratorColorAssignments?.[Number(collaborator.id)] ?? collaborator.color ?? lock?.color ?? '#64748b';

        return `position: relative; z-index: 2; outline: 2px solid ${color}aa; outline-offset: 2px; --tw-shadow: 0 0 12px 4px ${color}55;`;
    },
    isResultFieldDisabled(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        return Boolean(lock && lock.client_id !== clientId);
    },
    resultFieldLockLabel(fieldKey) {
        const lock = this.fieldLocks[fieldKey];

        return lock && lock.client_id !== clientId ? `${lock.user_name} is editing this field` : '';
    },
    async initCollaboration() {
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
        this.bindFieldLockDomSync();

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
                window.setTimeout(() => this.broadcastActiveResultFieldLock(), 200);
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

                window.Livewire.find(componentId)?.call('refreshSharedDraft', event);
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
    destroy() {
        this.stopPlayerPickerViewportSync();
        this.stopResultFieldLockRenewal();

        if (this.connectionStateTimeoutId) {
            window.clearTimeout(this.connectionStateTimeoutId);
            this.connectionStateTimeoutId = null;
        }

        if (this.foregroundSyncTimeoutId) {
            window.clearTimeout(this.foregroundSyncTimeoutId);
            this.foregroundSyncTimeoutId = null;
        }

        this.connectionCleanup?.();
        this.connectionCleanup = null;
        this.foregroundSyncCleanup?.();
        this.foregroundSyncCleanup = null;
        this.hasBoundForegroundSync = false;

        Object.values(this.fieldLockExpiryTimers).forEach((timerId) => window.clearTimeout(timerId));
        this.fieldLockExpiryTimers = {};
        Object.keys(this.fieldLockBlurTimeouts).forEach((timerId) => window.clearTimeout(Number(timerId)));
        this.fieldLockBlurTimeouts = {};

        if (this.fieldLockDomSyncFrameId) {
            window.cancelAnimationFrame?.(this.fieldLockDomSyncFrameId);
            window.clearTimeout(this.fieldLockDomSyncFrameId);
            this.fieldLockDomSyncFrameId = null;
        }

        this.fieldLockDomObserver?.disconnect();
        this.fieldLockDomObserver = null;
        this.livewireFieldLockHookCleanup?.();
        this.livewireFieldLockHookCleanup = null;
        this.recoveryCleanup?.();
        this.recoveryCleanup = null;

        if (this.saveTimeoutId) {
            window.clearTimeout(this.saveTimeoutId);
            this.saveTimeoutId = null;
        }

        window.Echo?.leave?.(channelName);
        this.fieldLockChannel = null;
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
            }, 900);
        });
    },
});

const resultFormEditors = (initialCollaborators = [], colorPalette = []) => ({
    collaboratorColorPalette: Array.isArray(colorPalette) ? colorPalette : [],
    collaboratorColorAssignments: {},
    collaboratorsUi: [],
    initEditors() {
        this.collaboratorsUi = [];
        this.normalizedCollaborators(initialCollaborators).forEach((member) => {
            this.collaboratorJoinedUi({ ...member, color: '#64748b' }, false);
        });
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
            .sort((first, second) => Number(first.joined_at ?? 0) - Number(second.joined_at ?? 0) || first.id - second.id);
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
        const normalizedMembers = this.normalizedCollaborators(members.filter((member) => !member.isLeaving));
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
            existingCollaborator.joined_at = member.joined_at ?? existingCollaborator.joined_at;
            existingCollaborator.isLeaving = false;
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
            joined_at: member.joined_at ?? 0,
            isLeaving: false,
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
        collaborator.isLeaving = true;
        this.rebalanceCollaboratorColors(this.collaboratorsUi.filter((entry) => entry.id !== collaboratorId));

        window.setTimeout(() => {
            if (!collaborator.isLeaving) {
                return;
            }

            this.collaboratorsUi = this.collaboratorsUi.filter((entry) => entry.id !== collaboratorId);
            this.rebalanceCollaboratorColors();
        }, 220);
    },
});

const resultFormRecovery = ({ componentId, fixtureId, draftVersion, isLocked }) => ({
    currentDraftVersion: Number(draftVersion ?? 0),
    storageKey: `result-form-recovery:${fixtureId}`,
    saveTimeoutId: null,
    recoveryCleanup: null,
    initRecovery() {
        if (isLocked) {
            this.clearSavedDraft();

            return;
        }

        const restored = this.restoreSavedDraft();

        if (! restored) {
            this.persistSavedDraft();
        }

        const handleChange = () => this.queueSavedDraft();
        const handleInput = () => this.queueSavedDraft();

        this.$el.addEventListener('change', handleChange, true);
        this.$el.addEventListener('input', handleInput, true);

        this.recoveryCleanup = () => {
            this.$el.removeEventListener('change', handleChange, true);
            this.$el.removeEventListener('input', handleInput, true);
        };
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
            return false;
        }

        const savedFrames = this.normalizeFrames(payload.frames ?? {});

        if (isDeepEqual(savedFrames, this.readFramesFromDom())) {
            return false;
        }

        const component = window.Livewire.find(componentId);

        if (! component) {
            return false;
        }

        component.call('restoreClientDraft', savedFrames, this.currentDraftVersion);

        return true;
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
        const frames = Array.from(this.$el.querySelectorAll('[data-result-frame-field]')).reduce((frames, field) => {
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

            if (valueType === 'score' && field.type === 'radio' && !field.checked) {
                return frames;
            }

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

        return this.normalizeFrames(frames);
    },
    normalizePlayerId(value) {
        if (value === null || value === '') {
            return null;
        }

        const playerId = Number(value);

        return Number.isFinite(playerId) ? playerId : null;
    },
    normalizeFrames(frames = {}) {
        return Array.from({ length: 10 }, (_, index) => index + 1).reduce((normalizedFrames, frameNumber) => {
            const frame = frames[frameNumber] ?? frames[String(frameNumber)] ?? {};

            normalizedFrames[frameNumber] = {
                home_player_id: this.normalizePlayerId(frame.home_player_id ?? null),
                away_player_id: this.normalizePlayerId(frame.away_player_id ?? null),
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

const resultFormPlayerPicker = ({ componentId, playerOptions = {}, awardedAvatarUrl }) => ({
    playerOptions,
    awardedAvatarUrl,
    playerPickerSelections: {},
    playerPickerOpen: false,
    playerPickerSearch: '',
    playerPickerFieldKey: null,
    playerPickerFrameNumber: null,
    playerPickerSide: null,
    playerPickerSelectedId: '',
    playerPickerReturnFocus: null,
    playerPickerViewportStyle: '',
    playerPickerViewportFrameId: null,
    playerPickerViewportHandler: null,
    playerPickerListId() {
        return `${componentId}-player-picker-options`;
    },
    syncPlayerPickerViewport() {
        const viewport = window.visualViewport;
        const top = viewport?.offsetTop ?? 0;
        const height = viewport?.height ?? window.innerHeight;

        this.playerPickerViewportStyle = `top: ${top}px; height: ${Math.max(0, height)}px;`;
    },
    schedulePlayerPickerViewportSync() {
        if (this.playerPickerViewportFrameId !== null) {
            window.cancelAnimationFrame(this.playerPickerViewportFrameId);
        }

        this.playerPickerViewportFrameId = window.requestAnimationFrame(() => {
            this.playerPickerViewportFrameId = null;
            this.syncPlayerPickerViewport();
        });
    },
    startPlayerPickerViewportSync() {
        if (this.playerPickerViewportHandler) {
            return;
        }

        this.syncPlayerPickerViewport();
        this.playerPickerViewportHandler = () => this.schedulePlayerPickerViewportSync();

        window.visualViewport?.addEventListener('resize', this.playerPickerViewportHandler);
        window.visualViewport?.addEventListener('scroll', this.playerPickerViewportHandler);
        window.addEventListener('resize', this.playerPickerViewportHandler);
    },
    stopPlayerPickerViewportSync() {
        if (this.playerPickerViewportFrameId !== null) {
            window.cancelAnimationFrame(this.playerPickerViewportFrameId);
            this.playerPickerViewportFrameId = null;
        }

        if (!this.playerPickerViewportHandler) {
            this.playerPickerViewportStyle = '';

            return;
        }

        window.visualViewport?.removeEventListener('resize', this.playerPickerViewportHandler);
        window.visualViewport?.removeEventListener('scroll', this.playerPickerViewportHandler);
        window.removeEventListener('resize', this.playerPickerViewportHandler);
        this.playerPickerViewportHandler = null;
        this.playerPickerViewportStyle = '';
    },
    filteredPlayerPickerPlayers() {
        const players = [
            { id: 0, name: 'Awarded', avatarUrl: awardedAvatarUrl, isAwarded: true },
            ...(this.playerOptions[this.playerPickerSide] ?? []),
        ];
        const search = this.playerPickerSearch.trim().toLowerCase();

        if (!search) {
            return players;
        }

        return players.filter((player) => player.name.toLowerCase().includes(search));
    },
    playerPickerInput() {
        if (!this.playerPickerFrameNumber || !['home', 'away'].includes(this.playerPickerSide)) {
            return null;
        }

        return this.$el.querySelector(
            `select[data-result-frame-field][data-frame-number="${this.playerPickerFrameNumber}"][data-frame-side="${this.playerPickerSide}"][data-frame-value="player"]`,
        );
    },
    syncPlayerPickerSelections() {
        if (!this.$el?.querySelectorAll) {
            return;
        }

        const selections = {};

        this.$el.querySelectorAll('select[data-result-frame-field][data-frame-value="player"]').forEach((select) => {
            const fieldKey = select.dataset.resultFieldKey;

            if (fieldKey) {
                selections[fieldKey] = select.value;
            }
        });

        this.playerPickerSelections = selections;
    },
    syncPlayerPickerSelection(event) {
        const select = event.currentTarget;
        const fieldKey = select?.dataset?.resultFieldKey;

        if (fieldKey) {
            this.playerPickerSelections[fieldKey] = select.value;
        }
    },
    syncOpposingPlayerPickerSelection(select) {
        const frameNumber = select?.dataset?.frameNumber;
        const side = select?.dataset?.frameSide;

        if (!frameNumber || !['home', 'away'].includes(side)) {
            return;
        }

        const opposingSide = side === 'home' ? 'away' : 'home';
        const opposingSelect = this.$el.querySelector(
            `select[data-result-frame-field][data-frame-number="${frameNumber}"][data-frame-side="${opposingSide}"][data-frame-value="player"]`,
        );

        if (!opposingSelect) {
            return;
        }

        if (select.value === '0') {
            opposingSelect.value = '0';
        } else if (opposingSelect.value === '0') {
            opposingSelect.value = '';
        }

        this.syncPlayerPickerSelection({ currentTarget: opposingSelect });
    },
    playerPickerSelection(fieldKey) {
        const selectedId = String(this.playerPickerSelections[fieldKey] ?? '');

        if (!selectedId) {
            return null;
        }

        if (selectedId === '0') {
            return { name: 'Awarded', avatarUrl: this.awardedAvatarUrl };
        }

        const side = fieldKey.endsWith('.home_player_id') ? 'home' : 'away';

        return (this.playerOptions[side] ?? []).find((player) => String(player.id) === selectedId) ?? null;
    },
    playerPickerSelectionLabel(fieldKey) {
        return this.playerPickerSelection(fieldKey)?.name ?? 'Select player...';
    },
    playerPickerSelectionAvatar(fieldKey) {
        return this.playerPickerSelection(fieldKey)?.avatarUrl ?? null;
    },
    handlePlayerPickerClick(event) {
        const action = event.target.closest?.('[data-result-player-picker-action]');

        if (!action || !this.$el.contains(action)) {
            return;
        }

        switch (action.dataset.resultPlayerPickerAction) {
            case 'open':
                this.openPlayerPicker(
                    action.dataset.resultFieldKey,
                    action.dataset.frameNumber,
                    action.dataset.playerSide,
                    { currentTarget: action },
                );
                break;
            case 'select':
                this.selectPlayer(action.dataset.playerId);
                break;
            case 'clear':
                this.clearPlayerPickerSelection();
                break;
            case 'close':
                this.closePlayerPicker();
                break;
            default:
                break;
        }
    },
    openPlayerPicker(fieldKey, frameNumber, side, event) {
        const select = this.$el.querySelector(
            `select[data-result-frame-field][data-frame-number="${frameNumber}"][data-frame-side="${side}"][data-frame-value="player"]`,
        );

        if (!select || select.disabled || this.isResultFieldDisabled(fieldKey) || this.formLocked || !this.formCanEdit) {
            return;
        }

        this.playerPickerFieldKey = fieldKey;
        this.playerPickerFrameNumber = Number(frameNumber);
        this.playerPickerSide = side;
        this.playerPickerSelectedId = select.value;
        this.playerPickerSearch = '';
        this.playerPickerReturnFocus = event?.currentTarget ?? null;
        this.startPlayerPickerViewportSync();
        this.playerPickerOpen = true;

        this.$nextTick(() => this.$refs.playerPickerSearch?.focus());
    },
    playerPickerIsDisabled() {
        return !this.playerPickerFieldKey
            || this.formLocked
            || !this.formCanEdit
            || this.isResultFieldDisabled(this.playerPickerFieldKey);
    },
    selectPlayer(playerId) {
        const select = this.playerPickerInput();

        if (!select || select.disabled || this.playerPickerIsDisabled()) {
            return;
        }

        select.value = playerId === null ? '' : String(playerId);
        this.syncPlayerPickerSelection({ currentTarget: select });
        this.syncOpposingPlayerPickerSelection(select);
        select.dispatchEvent(new Event('change', { bubbles: true }));
        this.closePlayerPicker();
    },
    clearPlayerPickerSelection() {
        this.selectPlayer(null);
    },
    closePlayerPicker() {
        if (!this.playerPickerOpen) {
            return;
        }

        const fieldKey = this.playerPickerFieldKey;
        const returnFocus = this.playerPickerReturnFocus;

        this.playerPickerOpen = false;
        this.playerPickerSearch = '';
        this.playerPickerFieldKey = null;
        this.playerPickerFrameNumber = null;
        this.playerPickerSide = null;
        this.playerPickerSelectedId = '';
        this.playerPickerReturnFocus = null;
        this.stopPlayerPickerViewportSync();

        this.$nextTick(() => {
            const trigger = returnFocus?.isConnected
                ? returnFocus
                : this.$el.querySelector(`[data-player-picker-trigger][data-result-field-key="${fieldKey}"]`);

            trigger?.focus({ preventScroll: true });
        });
    },
    focusFirstPlayerPickerOption() {
        this.$refs.playerPickerOptions?.querySelector('[data-result-player-option]:not(:disabled)')?.focus();
    },
    trapPlayerPickerFocus(event) {
        if (!this.playerPickerOpen || event.key !== 'Tab') {
            return;
        }

        const focusableElements = Array.from(this.$refs.playerPickerDialog?.querySelectorAll(
            'button:not(:disabled), input:not(:disabled)',
        ) ?? []);

        if (focusableElements.length === 0) {
            event.preventDefault();

            return;
        }

        const firstElement = focusableElements[0];
        const lastElement = focusableElements.at(-1);

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    },
});

window.resultForm = ({
    componentId,
    channelName,
    clientId,
    collaboratorId,
    collaboratorName,
    collaboratorColor,
    formLocked,
    formCanEdit,
    fixtureId,
    draftVersion,
    isLocked,
    collaborators = [],
    colorPalette = [],
    playerOptions = {},
    awardedAvatarUrl,
}) => ({
    ...resultFormCollaboration({
        componentId,
        channelName,
        clientId,
        collaboratorId,
        collaboratorName,
        collaboratorColor,
        formLocked,
        formCanEdit,
    }),
    ...resultFormEditors(collaborators, colorPalette),
    ...resultFormRecovery({
        componentId,
        fixtureId,
        draftVersion,
        isLocked,
    }),
    ...resultFormPlayerPicker({
        componentId,
        playerOptions,
        awardedAvatarUrl,
    }),
    init() {
        this.initEditors();
        this.initRecovery();
        this.syncPlayerPickerSelections();

        return this.initCollaboration();
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
