<script setup lang="ts" generic="T extends ItemProps">
import { GridLayout, type ItemProps } from '@/Components/Layout';

const props = defineProps<{
    modelValue: T[];
    routeKey: string;
}>();
</script>

<template>
    <GridLayout v-bind="props" class="[grid-template-columns:repeat(auto-fill,_minmax(360px,_1fr))] p-4">
        <template #default="{ item }: { item: T }">
            <slot :item="item" />
        </template>
    </GridLayout>
</template>
