export { default as AppLayout } from './AppLayout.vue';
export { default as AppSidebarLayout } from './AppSidebarLayout.vue';
export { default as AuthLayout } from './AuthLayout.vue';
export { default as useLayout } from './useLayout';

export type AuthLayoutProps = {
    title?: string;
    description?: string;
};
