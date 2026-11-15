import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'AstroHealth';

/** Pantallas a pantalla completa, sin el layout de la app (no exigen sesión). */
const FULL_SCREEN_PAGES = ['Identidad/Login'];

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Cada módulo tiene su carpeta con un "principal" (la página) y sus
    // Components/: "Identidad/Login" → resources/js/modules/Identidad/Login.vue
    pages: { path: './modules', extension: '.vue' },
    layout: (name) =>
        // AppLayout exige sesión: si no hay, manda a /acceso.
        FULL_SCREEN_PAGES.includes(name) ? null : AppLayout,
    withApp: (app) => {
        app.use(createPinia());

        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#38bdf8',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
