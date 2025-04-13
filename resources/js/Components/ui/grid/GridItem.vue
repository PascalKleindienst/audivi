<script setup lang="ts">
import { type HTMLAttributes, ref } from 'vue';
import { Primitive, type PrimitiveProps } from 'radix-vue';
import { cn } from '@/Utils';

const isFocused = ref(false);

interface Props extends PrimitiveProps {
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    as: 'div'
});
</script>

<template>
    <Primitive
        :as="as"
        :as-child="asChild"
        tabindex="-1"
        data-grid-item
        :data-highlighted="isFocused ? '' : undefined"
        :class="
            cn(
                'space-y-2 transition bg-card text-card-foreground flex flex-col gap-6 rounded-xl border shadow-sm p-4 hover:scale-105 hover:ring-2 hover:ring-ring focus:scale-105 focus:ring-2 focus:ring-ring focus-visible:outline-hidden',
                props.class
            )
        "
        @focus="isFocused = true"
        @blur="isFocused = false"
    >
        <slot />
    </Primitive>
</template>
