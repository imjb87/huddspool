// The offline shell uses the same Vite-built JavaScript as the Laravel page.
(async () => {
    try {
        const response = await fetch('/build/manifest.json');
        if (!response.ok) throw new Error('The game build manifest is unavailable.');
        const manifest = await response.json();
        const entry = manifest['public/games/8-ball/v1/game.mjs'];
        if (!entry?.file || !/^assets\/[a-zA-Z0-9_.-]+\.js$/.test(entry.file)) {
            throw new Error('The game build is missing. Rebuild the application assets before deploying.');
        }
        await import(`/build/${entry.file}`);
    } catch (error) {
        const status = document.getElementById('status');
        status.textContent = `Unable to start the table: ${error.message}`;
        status.setAttribute('role', 'alert');
        console.error('[huddspool:startup]', error);
    }
})();
