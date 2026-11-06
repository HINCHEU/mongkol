import { usePage } from '@inertiajs/vue3';

/**
 * Dashboard copy from lang/{km,en}/dashboard.php, shared by HandleInertiaRequests for the user's
 * language (English fills any gap). t('guests.title'), t('guests.count', { count: 3 }) → ":count" replaced.
 * A missing key shows itself, so it's easy to spot.
 */
export function t(key, replace = {}) {
    const value = key.split('.').reduce((node, part) => node?.[part], usePage().props.translations);
    if (typeof value !== 'string') return key;

    return Object.entries(replace).reduce((text, [name, v]) => text.replaceAll(`:${name}`, v), value);
}

export default {
    install(app) {
        app.config.globalProperties.$t = t;
    },
};
