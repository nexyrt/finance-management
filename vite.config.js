import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [`resources/views/**/*`,
                `./vendor/masmerise/livewire-toaster/resources/views/*.blade.php`
            ],
        }),
        tailwindcss(),
    ],
    content: [
        './vendor/masmerise/livewire-toaster/resources/views/*.blade.php',
    ],
    server: {
        cors: true,
    },
});