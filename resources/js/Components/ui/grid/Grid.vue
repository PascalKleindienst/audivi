<script setup lang="ts">
import { type HTMLAttributes, ref } from 'vue';
import { useEventListener } from '@vueuse/core';
import { ArrowNavigationOptions, useGridNavigation } from '@/Composables/gridNavigation';
import { cn } from '@/Utils';

interface Props {
    class?: HTMLAttributes['class'];
    options?: ArrowNavigationOptions;
}

const grid = ref<HTMLElement>();
const props = defineProps<Props>();

useEventListener<'keydown'>(document, 'keydown', (e: KeyboardEvent) => {
    if (
        e.key === 'ArrowUp' ||
        e.key === 'ArrowDown' ||
        e.key === 'ArrowRight' ||
        e.key === 'ArrowLeft' ||
        e.key === 'Home' ||
        e.key === 'End'
    ) {
        useGridNavigation(e, document.activeElement as HTMLElement, grid.value, props.options);
    }
});
</script>

<template>
    <div
        ref="grid"
        :class="cn('grid gap-6 [grid-template-columns:repeat(auto-fill,_minmax(250px,_1fr))]', props.class)"
    >
        <slot />
    </div>
</template>
