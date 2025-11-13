import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { glob } from 'glob';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/images/home/hero/terre-vue-espace.png',
                'resources/images/logo_explo_space.webp',
                'resources/images/backgroud_map.webp',
                ...glob.sync('resources/css/**/index.css'),
                ...glob.sync('resources/js/**/index.js'),
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});
