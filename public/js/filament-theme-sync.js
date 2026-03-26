(() => {
    const storageKey = 'site-theme';

    const getStoredTheme = () => {
        const storedTheme = window.localStorage.getItem(storageKey);

        return storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : null;
    };

    const currentTheme = () => document.documentElement.classList.contains('dark') ? 'dark' : 'light';

    const applyThemeMetadata = (theme) => {
        document.documentElement.dataset.theme = theme;
        document.documentElement.style.colorScheme = theme === 'dark' ? 'dark' : 'light';
    };

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';

        document.documentElement.classList.toggle('dark', isDark);
        applyThemeMetadata(theme);
    };

    const persistTheme = (theme) => {
        if (theme !== 'dark' && theme !== 'light') {
            return;
        }

        window.localStorage.setItem(storageKey, theme);
        window.dispatchEvent(new CustomEvent('site-theme-changed', {
            detail: {
                theme,
            },
        }));
    };

    const syncStoredTheme = () => {
        const storedTheme = getStoredTheme();

        if (storedTheme) {
            applyTheme(storedTheme);
        } else {
            persistTheme(currentTheme());
        }
    };

    syncStoredTheme();

    new MutationObserver(() => {
        const theme = currentTheme();

        if (document.documentElement.dataset.theme !== theme) {
            applyThemeMetadata(theme);
        }

        if (getStoredTheme() !== theme) {
            persistTheme(theme);
        }
    }).observe(document.documentElement, {
        attributeFilter: ['class'],
        attributes: true,
    });

    window.addEventListener('storage', (event) => {
        if (event.key !== storageKey) {
            return;
        }

        applyTheme(getStoredTheme() ?? currentTheme());
    });
})();
