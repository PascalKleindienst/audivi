<script setup lang="ts">
import AppLayout from '@/Layouts/AppSidebarLayout.vue';
import type { SharedProps } from '@/types/inertia';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedProps>();
const breadcrumbs = computed(() => page.props.breadcrumbs);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
