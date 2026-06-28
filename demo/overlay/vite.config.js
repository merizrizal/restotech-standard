import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/pos/app.js'],
            refresh: ['app/**/*.php', 'resources/views/**/*.blade.php', 'routes/**/*.php'],
        }),
    ],
});
