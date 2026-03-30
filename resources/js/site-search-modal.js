export function enhanceSiteSearch(component) {
    Object.assign(component, {
        initializeSiteSearch() {
            this.$watch('searchTerm', (value) => {
                this.scheduleSearch(value);
            });
        },
        openLoadedSearch() {
            this.open = true;
            this.searchTerm = '';
            this.resultGroups = [];
            this.isLoading = false;
            this.focusInput();
        },
        closeLoadedSearch() {
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
                const response = await window.fetch(`${this.endpoint}?${new URLSearchParams({ q: query }).toString()}`, {
                    signal: this.abortController.signal,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const payload = response.ok ? await response.json() : { groups: [] };

                this.resultGroups = payload.groups ?? [];
            } catch (error) {
                if (error.name !== 'CanceledError' && error.name !== 'AbortError') {
                    this.resultGroups = [];
                }
            } finally {
                this.abortController = null;
                this.isLoading = false;
            }
        },
    });
}

window.enhanceSiteSearch = enhanceSiteSearch;
