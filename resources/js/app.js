import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { formatCurrency, formatDate, formatNumber, formatRelativeTime } from './utils/formatters';

const appName = import.meta.env.VITE_APP_NAME || 'سوق الإعلانات';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        document.documentElement.setAttribute('dir', 'rtl');
        document.documentElement.setAttribute('lang', 'ar');

        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        vueApp.use(ZiggyVue);

        vueApp.config.globalProperties.$formatCurrency = formatCurrency;
        vueApp.config.globalProperties.$formatNumber = formatNumber;
        vueApp.config.globalProperties.$formatDate = formatDate;
        vueApp.config.globalProperties.$formatRelativeTime = formatRelativeTime;

        vueApp.mount(el);

        return vueApp;
    },
    progress: {
        color: '#F59E0B',
    },
});
