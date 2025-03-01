import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ['apexcharts']
    },
    build: {
        commonjsOptions: {
            include: ['node_modules/**']
        }
    },
    server: {
        hmr: {
            host: 'walletsaga.test'
        }
    }
});

