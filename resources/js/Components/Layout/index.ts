export { default as GridLayout } from './GridLayout.vue';
export { default as LayoutSwitcher } from './LayoutSwitcher.vue';
export { default as ListingLayout } from './ListingLayout.vue';
export { default as ListLayout } from './ListLayout.vue';

export type LayoutType = 'grid' | 'list';
export type ItemProps = { id: string | number; [key: string]: unknown };

import { useStorage } from '@vueuse/core';

export const useLayout = () => {
    const layoutState = useStorage<LayoutType>('layout-type', 'grid');

    return {
        layout: layoutState
    };
};
