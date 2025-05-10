<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { Separator } from '@/Components/ui/separator';

defineProps<{
    title?: string;
    subtitle?: string;
}>();
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="md:basis-1/2">
            <h2 v-if="title" class="text-2xl font-bold tracking-tight">{{ trans(title) }}</h2>
            <p v-if="subtitle" class="text-muted-foreground">{{ trans(subtitle) }}</p>
            <slot />
        </div>
        <div v-if="$slots.actions">
            <slot name="actions" />
        </div>
    </div>
    <Separator class="my-6" />
</template>
