window.homeLiveScoresMotion = () => ({
    observer: null,
    rowSignatures: new Map(),
    animationTimeouts: new Map(),
    init() {
        const list = this.$refs.list;

        if (!list) {
            return;
        }

        this.seedRows(list);

        if (typeof MutationObserver === 'undefined') {
            return;
        }

        this.observer = new MutationObserver((mutations) => {
            const rows = new Set();

            mutations.forEach((mutation) => {
                if (mutation.type === 'characterData') {
                    const row = mutation.target.parentElement?.closest('[data-home-live-score-row]');

                    if (row) {
                        rows.add(row);
                    }

                    return;
                }

                mutation.addedNodes.forEach((node) => this.collectRows(node, rows));
            });

            rows.forEach((row) => this.syncRow(row));
        });

        this.observer.observe(list, {
            childList: true,
            subtree: true,
            characterData: true,
        });
    },
    destroy() {
        this.observer?.disconnect();

        this.animationTimeouts.forEach((timeoutId) => window.clearTimeout(timeoutId));
        this.animationTimeouts.clear();
    },
    seedRows(list) {
        list.querySelectorAll('[data-home-live-score-row]').forEach((row) => {
            this.rowSignatures.set(this.rowKey(row), this.rowSignature(row));
        });
    },
    collectRows(node, rows) {
        if (node.nodeType !== 1) {
            return;
        }

        if (node.matches('[data-home-live-score-row]')) {
            rows.add(node);
        }

        node.querySelectorAll('[data-home-live-score-row]').forEach((row) => rows.add(row));
    },
    rowKey(row) {
        return row.dataset.homeLiveScoreKey ?? row.getAttribute('href') ?? row.textContent.trim();
    },
    rowSignature(row) {
        return row.querySelector('[data-home-live-score-pill]')?.textContent.trim() ?? row.textContent.trim();
    },
    syncRow(row) {
        const key = this.rowKey(row);
        const signature = this.rowSignature(row);
        const previousSignature = this.rowSignatures.get(key);

        this.rowSignatures.set(key, signature);

        if (previousSignature === undefined) {
            this.animateRow(row, 'ui-live-score-item--enter');
        } else if (previousSignature !== signature) {
            this.animateRow(row, 'ui-live-score-item--updated');
        }
    },
    animateRow(row, className) {
        const animationClasses = ['ui-live-score-item--enter', 'ui-live-score-item--updated'];
        const existingTimeout = this.animationTimeouts.get(row);

        if (existingTimeout) {
            window.clearTimeout(existingTimeout);
        }

        row.classList.remove(...animationClasses);

        window.requestAnimationFrame(() => {
            row.classList.add(className);

            this.animationTimeouts.set(row, window.setTimeout(() => {
                row.classList.remove(...animationClasses);
                this.animationTimeouts.delete(row);
            }, 800));
        });
    },
});
