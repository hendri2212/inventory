import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                        'resources/js/app.js',
                        'resources/css/all.min.css',
                        'resources/css/adminlte.min.css',
                        'resources/css/OverlayScrollbars.min.css',
                        'resources/css/summernote-bs4.min.css',
                        'resources/js/jquery.min.js',
                        'resources/js/adminlte.js'
                    ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap')
        }
    }
});
