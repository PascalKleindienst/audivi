import { AxiosInstance } from 'axios';
import { trans } from 'laravel-vue-i18n';
// import { Config as ZiggyConfig } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    // const Ziggy: ZiggyConfig;
    const route: typeof routeFn;

    const $t: typeof trans;
}
