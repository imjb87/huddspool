const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';

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

    if (! response.ok) {
        throw new Error(`Request failed with status ${response.status}.`);
    }

    return response;
};

export const registerHeaderNotificationsStore = (Alpine) => {
    Alpine.store('headerNotifications', {
        initialized: false,
        loading: false,
        markingAll: false,
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
            if (! this.readAllUrl || this.markingAll) {
                return;
            }

            this.markingAll = true;

            try {
                const response = await request(this.readAllUrl, {
                    method: 'POST',
                });
                this.applyPayload(await response.json());
            } catch (error) {
                console.error('[header-notifications] Failed to mark all notifications as read.', error);
            } finally {
                this.markingAll = false;
            }
        },
        applyPayload(payload) {
            this.unreadCount = Number(payload?.unread_count ?? 0);
            this.notifications = Array.isArray(payload?.notifications) ? payload.notifications : [];
        },
    });
};

export const notificationsDrawer = () => ({
    open: false,
    configure(urls) {
        this.$store.headerNotifications.configure(urls);
    },
    toggle() {
        if (this.open) {
            this.close();

            return;
        }

        this.open = true;
        this.$store.headerNotifications.refresh();
    },
    close() {
        this.open = false;
    },
});
