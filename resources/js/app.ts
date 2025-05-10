import '../css/app.css';
import './bootstrap';

import { AppLayout } from '@/Layouts';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { i18nVue } from 'laravel-vue-i18n';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

// Extend ImportMeta interface for Vite...
// declare module 'vite/client' {
//     interface ImportMetaEnv {
//         readonly VITE_APP_NAME: string;
//         [key: string]: string | boolean | undefined;
//     }
//
//     interface ImportMeta {
//         readonly env: ImportMetaEnv;
//         readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
//     }
// }

const appName = import.meta.env.VITE_APP_NAME ?? 'Audivi';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        return await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')).then((page) => {
            page.default.layout = page.default.layout ?? AppLayout;
            return page;
        });
    },
    // ),
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-expect-error
    setup({ el, App, props, plugin }) {
        return (
            createApp({ render: () => h(App, props) })
                .use(plugin)
                // .use(ZiggyVue, Ziggy)
                .use(ZiggyVue)
                .use(i18nVue, {
                    fallbackLang: import.meta.env.VITE_APP_FALLBACK_LOCALE ?? 'en',
                    fallbackMissingTranslations: true,
                    resolve: async (lang: string) => {
                        const langs = import.meta.glob('../../lang/*.json');
                        return await langs[`../../lang/${lang}.json`]();
                    }
                })
                .mount(el)
        );
    },
    progress: {
        color: '#4B5563'
    }
});
