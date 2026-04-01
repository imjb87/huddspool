<script>
    (() => {
        const storageKey = 'theme';
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const transitionClass = 'theme-transitioning';
        let transitionTimeout = null;

        const getStoredTheme = () => {
            const storedTheme = window.localStorage.getItem(storageKey);

            return storedTheme === 'dark' || storedTheme === 'light' || storedTheme === 'system' ? storedTheme : null;
        };

        const resolveTheme = (themePreference) => {
            if (themePreference === 'system') {
                return mediaQuery.matches ? 'dark' : 'light';
            }

            return themePreference === 'dark' ? 'dark' : 'light';
        };

        const getPreferredTheme = () => {
            return getStoredTheme() ?? 'system';
        };

        const applyTheme = (themePreference) => {
            const resolvedTheme = resolveTheme(themePreference);
            const isDark = resolvedTheme === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = resolvedTheme;
            document.documentElement.dataset.themePreference = themePreference;
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        };

        const dispatchThemeChange = (themePreference) => {
            window.dispatchEvent(new CustomEvent('site-theme-changed', {
                detail: {
                    preference: themePreference,
                    theme: resolveTheme(themePreference),
                },
            }));
        };

        const setTheme = (themePreference) => {
            startThemeTransition();

            if (themePreference === 'dark' || themePreference === 'light' || themePreference === 'system') {
                window.localStorage.setItem(storageKey, themePreference);
            } else {
                window.localStorage.removeItem(storageKey);
                themePreference = getPreferredTheme();
            }

            applyTheme(themePreference);
            dispatchThemeChange(themePreference);

            return themePreference;
        };

        const toggleTheme = () => {
            return setTheme(resolveTheme(getPreferredTheme()) === 'dark' ? 'light' : 'dark');
        };

        const startThemeTransition = () => {
            document.documentElement.classList.add(transitionClass);

            if (transitionTimeout) {
                window.clearTimeout(transitionTimeout);
            }

            transitionTimeout = window.setTimeout(() => {
                document.documentElement.classList.remove(transitionClass);
                transitionTimeout = null;
            }, 550);
        };

        window.siteTheme = {
            storageKey,
            getStoredTheme,
            getPreferredTheme,
            resolveTheme,
            applyTheme,
            setTheme,
            toggleTheme,
            currentTheme: () => resolveTheme(getPreferredTheme()),
            currentPreference: () => getPreferredTheme(),
        };

        applyTheme(getPreferredTheme());

        const syncSystemTheme = () => {
            const themePreference = getPreferredTheme();

            if (themePreference !== 'system') {
                return;
            }

            applyTheme(themePreference);
            dispatchThemeChange(themePreference);
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

            const themePreference = getPreferredTheme();

            applyTheme(themePreference);
            dispatchThemeChange(themePreference);
        });
    })();
</script>
