import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // En Docker: escuchar en todas las interfaces del contenedor...
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // ...pero el navegador (en tu equipo) llega por localhost:5173
        hmr: { host: 'localhost' },
        cors: true,
        watch: {
            // Los cambios de una carpeta de Windows no avisan al contenedor: hay que preguntar
            usePolling: true,
            interval: 500,
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
