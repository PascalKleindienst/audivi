<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { useVModel } from '@vueuse/core';
import { cn } from '@/Utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    defaultValue?: string | number | null;
    modelValue?: string | number | null;
}>();

const emits = defineEmits<(e: 'update:modelValue', payload: string | number) => void>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue
});
</script>

<template>
    <textarea
        v-model="modelValue"
        :class="
            cn(
                'flex min-h-20 w-full rounded-md border border-border bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                props.class
            )
        "
    />
</template>
