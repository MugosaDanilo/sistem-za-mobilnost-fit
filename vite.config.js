import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    base: '/build/',  // dodaj /build/ kako bi bili sigurni da su svi resursi sa pravilnim putem
    server: {
        https: true,  // osiguraj da je Vite server postavljen na HTTPS (ako se koristi lokalno ili u razvoju)
    },
});
