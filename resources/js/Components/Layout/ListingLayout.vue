<script setup lang="ts" generic="T extends ItemProps">
import { GridLayout, type ItemProps, LayoutSwitcher, ListLayout, useLayout } from '@/Components/Layout';

const { layout } = useLayout();

const props = defineProps<{
    modelValue: T[];
    routeKey: string;
}>();
</script>

<template>
    <LayoutSwitcher />

    <GridLayout v-if="layout === 'grid'" v-bind="props">
        <template #default="{ item }: { item: T }">
            <slot name="grid" :item="item" />
        </template>
    </GridLayout>

    <ListLayout v-if="layout === 'list'" v-bind="props">
        <template #default="{ item }: { item: T }">
            <slot name="list" :item="item" />
        </template>
    </ListLayout>
</template>
