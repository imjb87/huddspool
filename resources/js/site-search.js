let siteSearchRegistered = false;

/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerSiteSearch(Alpine) {
    if (siteSearchRegistered) {
        return;
    }

    siteSearchRegistered = true;

    Alpine.data('siteSearch', (endpoint) => ({
        endpoint,
        open: false,
        searchTerm: '',
        resultGroups: [],
        isLoading: false,
        focusTimer: null,
        searchTimer: null,
        abortController: null,
        async init() {
            this.$watch('searchTerm', (value) => {
                this.scheduleSearch(value);
            });
        },
        openSearch() {
            this.open = true;
            this.searchTerm = '';
            this.resultGroups = [];
            this.isLoading = false;
            this.focusInput();
        },
        close() {
            if (this.focusTimer) {
                window.clearTimeout(this.focusTimer);
                this.focusTimer = null;
            }

            if (this.searchTimer) {
                window.clearTimeout(this.searchTimer);
                this.searchTimer = null;
            }

            this.abortPendingRequest();
            this.open = false;
            this.searchTerm = '';
            this.resultGroups = [];
            this.isLoading = false;
        },
        focusInput() {
            if (this.focusTimer) {
                window.clearTimeout(this.focusTimer);
            }

            this.focusTimer = window.setTimeout(() => {
                this.$refs.searchInput?.focus({ preventScroll: true });
                this.focusTimer = null;
            }, 75);
        },
        scheduleSearch(value) {
            const trimmedValue = typeof value === 'string' ? value.trim() : '';

            if (this.searchTimer) {
                window.clearTimeout(this.searchTimer);
                this.searchTimer = null;
            }

            if (trimmedValue.length < 3) {
                this.abortPendingRequest();
                this.resultGroups = [];
                this.isLoading = false;

                return;
            }

            this.isLoading = true;
            this.searchTimer = window.setTimeout(() => {
                this.performSearch(trimmedValue);
                this.searchTimer = null;
            }, 300);
        },
        abortPendingRequest() {
            if (! this.abortController) {
                return;
            }

            this.abortController.abort();
            this.abortController = null;
        },
        async performSearch(query) {
            this.abortPendingRequest();
            this.abortController = new AbortController();

            try {
                const response = await window.axios.get(this.endpoint, {
                    params: { q: query },
                    signal: this.abortController.signal,
                });

                this.resultGroups = response.data.groups ?? [];
            } catch (error) {
                if (error.name !== 'CanceledError' && error.name !== 'AbortError') {
                    this.resultGroups = [];
                }
            } finally {
                this.abortController = null;
                this.isLoading = false;
            }
        },
    }));

    if (window.siteSearchBindingsRegistered) {
        return;
    }

    window.siteSearchBindingsRegistered = true;

    const dispatchSiteSearchOpen = (event = null) => {
        if (event) {
            event.preventDefault();
        }

        window.dispatchEvent(new CustomEvent('site-search:open'));
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-site-search-trigger]');

        if (! trigger) {
            return;
        }

        dispatchSiteSearchOpen(event);
    });

    document.addEventListener('keydown', (event) => {
        if (! (event.metaKey || event.ctrlKey)) {
            return;
        }

        if (event.key.toLowerCase() !== 'k') {
            return;
        }

        dispatchSiteSearchOpen(event);
    });
}
