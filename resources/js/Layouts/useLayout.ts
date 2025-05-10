import { Component, VNode } from 'vue';

export default function useLayout<T = any>(layout: Component, props: T) {
    type LayoutHelper<T = any> = (component: Component, props: T, children?: () => any) => VNode;
    type DefineLayoutOptions<T = any> = {
        layout: (h: LayoutHelper<T>, page: VNode) => VNode;
    };

    return {
        layout: (h, page) => h(layout, props, () => page)
    } as DefineLayoutOptions<T>;
}
