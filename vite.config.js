import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // ovo osigurava da asseti imaju relativan path
    base: 'https://dakazzmobilnost.onrender.com/', // da bude HTTPS
});
