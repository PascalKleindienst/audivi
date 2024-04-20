<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
import { DialogDescription, type DialogDescriptionProps, useForwardProps } from 'radix-vue';
import { cn } from '@/Utils';

const props = defineProps<DialogDescriptionProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <DialogDescription v-bind="forwardedProps" :class="cn('text-sm text-muted-foreground', props.class)">
        <slot />
    </DialogDescription>
</template>
