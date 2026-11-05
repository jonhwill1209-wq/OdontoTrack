import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/modules/usuarios.js',
                'resources/js/modules/pacientes.js',
                'resources/js/modules/agenda.js',
                'resources/js/modules/expedientes.js',
                'resources/js/modules/dashboard.js',
                'resources/js/modules/reportes.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
