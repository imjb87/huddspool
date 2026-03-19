<script>
    (() => {
        const storageKey = 'site-theme';
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        const getStoredTheme = () => {
            const storedTheme = window.localStorage.getItem(storageKey);

            return storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : null;
        };

        const getPreferredTheme = () => {
            return getStoredTheme() ?? (mediaQuery.matches ? 'dark' : 'light');
        };

        const applyTheme = (theme) => {
            const isDark = theme === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = theme;
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        };

        const dispatchThemeChange = (theme) => {
            window.dispatchEvent(new CustomEvent('site-theme-changed', {
                detail: {
                    theme,
                },
            }));
        };

        const setTheme = (theme) => {
            if (theme === 'dark' || theme === 'light') {
                window.localStorage.setItem(storageKey, theme);
            } else {
                window.localStorage.removeItem(storageKey);
                theme = getPreferredTheme();
            }

            applyTheme(theme);
            dispatchThemeChange(theme);

            return theme;
        };

        const toggleTheme = () => {
            return setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
        };

        window.siteTheme = {
            storageKey,
            getStoredTheme,
            getPreferredTheme,
            applyTheme,
            setTheme,
            toggleTheme,
            currentTheme: () => document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        };

        applyTheme(getPreferredTheme());

        const syncSystemTheme = (event) => {
            if (getStoredTheme()) {
                return;
            }

            const theme = event.matches ? 'dark' : 'light';

            applyTheme(theme);
            dispatchThemeChange(theme);
        };

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', syncSystemTheme);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(syncSystemTheme);
        }

        window.addEventListener('storage', (event) => {
            if (event.key !== storageKey) {
                return;
            }

            const theme = getPreferredTheme();

            applyTheme(theme);
            dispatchThemeChange(theme);
        });
    })();
</script>
