import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { route } from 'ziggy-js';

// Make route() available globally (for convenience, mirrors Laravel's route() helper)
declare global {
    // eslint-disable-next-line no-var
    var route: typeof route;
}
window.route = route;

const appName = document.querySelector<HTMLMetaElement>('meta[name="app-name"]')?.content
    ?? 'Finance Management';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),

    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),

    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },

    progress: {
        color: '#3b82f6',
    },
});
