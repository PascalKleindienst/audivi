<script setup lang="ts">
import AppContent from '@/Components/App/AppContent.vue';
import AppShell from '@/Components/App/AppShell.vue';
import AppSidebar from '@/Components/App/AppSidebar.vue';
import AppSidebarHeader from '@/Components/App/AppSidebarHeader.vue';
import Banner from '@/Components/Banner.vue';
import ControlBar from '@/Components/Player/ControlBar.vue';
import BreadcrumbItemData = App.Data.BreadcrumbItemData;

interface Props {
    breadcrumbs?: BreadcrumbItemData[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => []
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar :key="route().current()" />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <Banner />
            <slot />
            <ControlBar />
        </AppContent>
    </AppShell>
</template>
