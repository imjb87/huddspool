export function enhanceSiteSearch(component) {
    Object.assign(component, {
        flattenedResults() {
            return this.resultGroups.flatMap((group) => {
                return (group.results ?? []).map((item) => ({
                    id: `site-search-result-${group.key}-${item.id}`,
                    href: item.href,
                }));
            });
        },
        activeResult() {
            return this.flattenedResults()[this.activeResultIndex] ?? null;
        },
        activeResultId() {
            return this.activeResult()?.id ?? null;
        },
        syncActiveResult() {
            const results = this.flattenedResults();

            if (results.length === 0) {
                this.activeResultIndex = -1;

                return;
            }

            if (this.activeResultIndex < 0 || this.activeResultIndex >= results.length) {
                this.activeResultIndex = 0;
            }
        },
        setActiveResultById(resultId) {
            const nextIndex = this.flattenedResults().findIndex((result) => result.id === resultId);

            if (nextIndex === -1) {
                return;
            }

            this.activeResultIndex = nextIndex;
        },
        moveActiveResult(direction) {
            const results = this.flattenedResults();

            if (results.length === 0) {
                return;
            }

            if (this.activeResultIndex === -1) {
                this.activeResultIndex = direction > 0 ? 0 : results.length - 1;
            } else {
                this.activeResultIndex = (this.activeResultIndex + direction + results.length) % results.length;
            }

            this.scrollActiveResultIntoView();
        },
        openActiveResult() {
            const activeResult = this.activeResult();

            if (!activeResult) {
                return;
            }

            window.location.assign(activeResult.href);
        },
        scrollActiveResultIntoView() {
            this.$nextTick(() => {
                document.getElementById(this.activeResultId())?.scrollIntoView({
                    block: 'nearest',
                });
            });
        },
        initializeSiteSearch() {
            this.$watch('searchTerm', (value) => {
                this.scheduleSearch(value);
            });
        },
        openLoadedSearch() {
            this.open = true;
            this.searchTerm = '';
            this.resultGroups = [];
            this.activeResultIndex = -1;
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
            this.activeResultIndex = -1;
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
                this.activeResultIndex = -1;
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
                this.syncActiveResult();
            } catch (error) {
                if (error.name !== 'CanceledError' && error.name !== 'AbortError') {
                    this.resultGroups = [];
                    this.activeResultIndex = -1;
                }
            } finally {
                this.abortController = null;
                this.isLoading = false;
            }
        },
    });
}

window.enhanceSiteSearch = enhanceSiteSearch;
