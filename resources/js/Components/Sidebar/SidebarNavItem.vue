<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';

defineProps<{ icon?: string; href: string; active?: boolean }>();
</script>

<template>
    <li>
        <Link
            :href="href"
            :class="{ 'border-l-4 border-primary bg-slate-200 dark:bg-gray-900': active }"
            class="flex w-full items-center gap-x-3 rounded p-2 text-sm hover:bg-slate-200 hover:dark:bg-gray-900"
        >
            <Icon v-if="icon" :icon="icon" class="size-5 flex-shrink-0" />
            <slot />
        </Link>
    </li>
</template>
