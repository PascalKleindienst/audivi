<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
import { ToastClose, type ToastCloseProps } from 'radix-vue';
import { X } from 'lucide-vue-next';
import { cn } from '@/Utils';

const props = defineProps<
    ToastCloseProps & {
        class?: HTMLAttributes['class'];
    }
>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ToastClose
        v-bind="delegatedProps"
        :class="
            cn(
                'absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity hover:text-foreground focus:opacity-100 focus:outline-hidden focus:ring-2 group-hover:opacity-100 group-[.destructive]:text-red-300 hover:group-[.destructive]:text-red-50 focus:group-[.destructive]:ring-red-400 focus:group-[.destructive]:ring-offset-red-600',
                props.class
            )
        "
    >
        <X class="h-4 w-4" />
    </ToastClose>
</template>
