<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { Separator } from '@/Components/ui/separator';

defineProps<{
    title?: string;
    subtitle?: string;
}>();
</script>

<template>
    <div>
        <h2 v-if="title" class="text-2xl font-bold tracking-tight">{{ trans(title) }}</h2>
        <p v-if="subtitle" class="text-muted-foreground">{{ trans(subtitle) }}</p>
        <slot />
        <Separator class="my-6" />
    </div>
</template>
