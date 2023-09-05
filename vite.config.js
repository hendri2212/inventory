import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js','plugins/fontawesome-free/css/all.min.css','resources/css/adminlte.min.css','plugins/overlayScrollbars/css/OverlayScrollbars.min.css','plugins/summernote/summernote-bs4.min.css'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap')
        }
    }
});
