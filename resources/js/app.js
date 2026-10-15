import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy'; // keep as in your repo

import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import { createVuetify } from 'vuetify';

const autoCareTheme = {
    dark: false,
    colors: {
        background: '#F6FAF7',
        surface: '#FFFFFF',
        primary: '#B4F8C8',       // neon light green: buttons, active states
        'on-primary': '#122323',  // dark text on lime buttons
        secondary: '#122323',     // deep forest green: header/sidebar
        'on-secondary': '#FFFFFF',
        accent: '#7BE0A0',
        error: '#D32F2F',
        info: '#2196F3',
        success: '#2E7D32',
        warning: '#FB8C00',
    },
};

const vuetify = createVuetify({
    theme: {
        defaultTheme: 'autoCare',
        themes: { autoCare: autoCareTheme },
    },
    defaults: {
        VBtn: { rounded: 'lg', class: 'text-none font-weight-medium', elevation: 0 },
        VTextField: { variant: 'outlined', rounded: 'lg', density: 'comfortable' },
        VSelect: { variant: 'outlined', rounded: 'lg', density: 'comfortable' },
        VCard: { rounded: 'xl', elevation: 1 },
    },
});

const appName = import.meta.env.VITE_APP_NAME || 'Auto Care';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .mount(el);
    },
    progress: { color: '#B4F8C8' },
});