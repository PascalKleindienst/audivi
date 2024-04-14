import { AxiosInstance } from 'axios';
import { Config as ZiggyConfig } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    const Ziggy: ZiggyConfig;
}
