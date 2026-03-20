<script>
    document.addEventListener('livewire:initialized', () => {
        if (window.siteSearchBindingsRegistered) {
            return;
        }

        window.siteSearchBindingsRegistered = true;

        const openSiteSearch = (event = null) => {
            if (event) {
                event.preventDefault();
            }

            Livewire.dispatch('openSearch');
        };

        document.querySelectorAll('[data-site-search-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', openSiteSearch);
        });

        document.addEventListener('keydown', (event) => {
            if (! (event.metaKey || event.ctrlKey)) {
                return;
            }

            if (event.key.toLowerCase() !== 'k') {
                return;
            }

            openSiteSearch(event);
        });
    });
</script>
