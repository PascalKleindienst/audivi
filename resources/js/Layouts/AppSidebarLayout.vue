<script setup lang="ts">
import AppContent from '@/Components/App/AppContent.vue';
import AppShell from '@/Components/App/AppShell.vue';
import AppSidebar from '@/Components/App/AppSidebar.vue';
import AppSidebarHeader from '@/Components/App/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import Banner from '@/Components/Banner.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => []
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <Banner />
            <slot />
        </AppContent>
    </AppShell>
</template>
