
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
    connectionHealth: 'healthy',
    connectionBadgeText: 'Live updates connected',
    connectionHeading: 'Live syncing is healthy',
    connectionMessage: '',
    statusClassName(status, classes) {
        return classes[status] ?? classes.healthy;
    },
    updateConnectionState(state) {
        const nextState = typeof state === 'string' ? state : state?.current;

        switch (nextState) {
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
    bindConnectionStatus() {
        const connection = window.Echo?.connector?.pusher?.connection;

        if (!connection) {
            this.updateConnectionState('failed');

            return;
        }

        this.updateConnectionState(connection.state);
        connection.bind('state_change', (states) => this.updateConnectionState(states.current));
        connection.bind('error', () => this.updateConnectionState('failed'));
    },
    init() {
        if (!window.Echo || !window.Livewire) {
            this.updateConnectionState('failed');
            console.warn('[result-collaboration] Echo or Livewire is unavailable; collaboration channel was not initialized.', {
                channelName,
            });

            return;
        }

        this.bindConnectionStatus();

        const syncUi = (members) => this.syncCollaboratorsUi?.(members);
        const joinUi = (member) => this.collaboratorJoinedUi?.(member);
        const leaveUi = (member) => this.collaboratorLeftUi?.(member);

        window.Echo.leave(channelName);

        window.Echo.join(channelName)
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

window.resultFormPresenceTooltip = () => ({
    open: false,
    isPositioned: false,
    tooltipStyle: '',
    showTooltip() {
        this.open = true;
        this.isPositioned = false;

        this.$nextTick(() => {
            this.positionTooltip();
            this.isPositioned = true;
        });
    },
    hideTooltip() {
        this.open = false;
        this.isPositioned = false;
    },
    positionTooltip() {
        if (!this.$refs.trigger || !this.$refs.tooltip) {
            return;
        }

        const viewportPadding = 8;
        const triggerBounds = this.$refs.trigger.getBoundingClientRect();
        const tooltipWidth = this.$refs.tooltip.offsetWidth;
        const centeredLeft = triggerBounds.left + (triggerBounds.width / 2);
        const clampedLeft = Math.max(
            viewportPadding + (tooltipWidth / 2),
            Math.min(window.innerWidth - viewportPadding - (tooltipWidth / 2), centeredLeft),
        );

        this.tooltipStyle = `left:${clampedLeft}px;top:${triggerBounds.top - 8}px;`;
    },
});
