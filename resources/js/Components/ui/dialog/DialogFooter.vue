<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/Utils';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <div
        :class="
            cn(
                'flex flex-col-reverse bg-secondary px-6 py-4 sm:flex-row sm:justify-end sm:gap-x-2 sm:rounded-b-lg',
                props.class
            )
        "
    >
        <slot />
    </div>
</template>
