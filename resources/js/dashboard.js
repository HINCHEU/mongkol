import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import i18n from './dashboard/i18n.js';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./dashboard/Pages/**/*.vue', { eager: true });
        return pages[`./dashboard/Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).use(i18n).mount(el);
    },
});
