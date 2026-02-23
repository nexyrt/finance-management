import {
    defineConfig,
    loadEnv
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const hmrHost = env.VITE_HMR_HOST || null;

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
            }),
            tailwindcss(),
        ],
        content: [
            './vendor/masmerise/livewire-toaster/resources/views/*.blade.php',
        ],
        server: {
            cors: true,
            host: '0.0.0.0',
            hmr: {
                host: hmrHost || 'localhost',
            },
            ...(hmrHost ? {
                origin: `http://${hmrHost}:5173`,
            } : {}),
        },
        build: {
            chunkSizeWarningLimit: 1500,
        },
    };
});
