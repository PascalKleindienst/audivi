<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/Utils';

const props = defineProps<{
    class?: HTMLAttributes['class']
}>();
</script>

<template>
    <div
        data-slot="alert-dialog-header"
        :class="cn('flex gap-2 text-center sm:text-left', props.class)"
    >
        <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            <svg
                class="h-6 w-6 text-red-600 dark:text-red-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                />
            </svg>
        </div>
        <div class="mt-3 text-center sm:ms-4 sm:mt-0 sm:text-start">
            <slot />
        </div>
    </div>
</template>
