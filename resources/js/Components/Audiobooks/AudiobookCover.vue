<script setup lang="ts">
import HeroPattern, { type HeroPatternColors, type HeroPatternVariants } from '@/Components/HeroPattern.vue';
import { cn } from '@/Utils';
import { Primitive } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    cover: string | null | undefined;
    title: string | null | undefined;
    pattern: number;
    class?: HTMLAttributes['class'];
}>();

const getPattern = (
    id: number
): {
    color: HeroPatternColors;
    variant: HeroPatternVariants;
} => {
    return {
        color: ['amber', 'red', 'blue', 'green', 'orange', 'indigo', 'teal'][id % 7] as HeroPatternColors,
        variant: ['glamorous', 'food', 'hexagons', 'random', 'anchors', 'bubbles', 'skulls'][id % 7] as HeroPatternVariants
    };
};
</script>

<template>
    <Primitive as-child :class="cn('[aspect-ratio:1/1] h-auto rounded-xl object-cover shadow-lg', props.class)">
        <img v-if="props.cover" :src="props.cover" :alt="props.title ?? ''" />
        <HeroPattern v-else :variant="getPattern(props.pattern).variant" :color="getPattern(props.pattern).color" />
    </Primitive>
</template>
