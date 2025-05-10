<script setup lang="ts">
import { cn } from '@/Utils';
import { type ButtonVariants, buttonVariants } from '@/Components/ui/button';
import { AlertDialogAction, type AlertDialogActionProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<AlertDialogActionProps & { class?: HTMLAttributes['class'], variant?: ButtonVariants['variant'] }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <AlertDialogAction v-bind="delegatedProps" :class="cn(buttonVariants({ variant: props.variant || 'destructive' }), props.class)">
        <slot />
    </AlertDialogAction>
</template>
