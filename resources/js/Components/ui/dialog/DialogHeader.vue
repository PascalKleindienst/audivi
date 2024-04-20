<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/Utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('flex flex-col gap-y-6 px-4 py-6 text-center sm:text-left', props.class)">
        <slot />
    </div>
</template>
