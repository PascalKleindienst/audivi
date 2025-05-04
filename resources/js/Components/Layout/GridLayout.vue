<script setup lang="ts" generic="T extends ItemProps">
import type { ItemProps } from '@/Components/Layout';
import { Grid, GridItem } from '@/Components/ui/grid';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: T[];
    routeKey: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: T];
}>();

// define proxyValue for modelValue to emit update:modelValue
const items = computed({
    get() {
        return props.modelValue;
    },
    set(value: T) {
        emit('update:modelValue', value);
    }
});
</script>

<template>
    <Grid class="p-4">
        <GridItem v-for="item in items" :key="item.id" as-child>
            <Link :href="route(routeKey, item.id)" @keydown.space.prevent="() => router.visit(route(routeKey, item.id))">
                <slot :item="item" />
            </Link>
        </GridItem>
    </Grid>
</template>
