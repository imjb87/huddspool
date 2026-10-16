import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/livewire-app.js', 'resources/js/site-search-modal.js', 'resources/js/pool-host.js'],
            refresh: true,
        }),
    ],
});
