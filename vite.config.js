import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/pos/app.js'],
            refresh: ['resources/views/**/*.blade.php', 'src/**/*.php', 'routes/**/*.php'],
        }),
    ],
});
