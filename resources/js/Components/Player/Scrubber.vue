<script setup lang="ts">
import { useEventListener, useMouseInElement } from '@vueuse/core';
import { shallowRef, useTemplateRef, watch } from 'vue';

const props = defineProps({
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    secondary: { type: Number, default: 0 }
});

const model = defineModel({ type: Number, required: true, default: 0 });

const scrubber = useTemplateRef('scrubber');
const scrubbing = shallowRef(false);
const pendingValue = shallowRef(0);

useEventListener('mouseup', () => (scrubbing.value = false), { passive: true });

const { elementX, elementWidth } = useMouseInElement(scrubber);

watch([scrubbing, elementX], () => {
    const progress = Math.max(0, Math.min(1, elementX.value / elementWidth.value));
    pendingValue.value = progress * props.max;
    if (scrubbing.value) {
        model.value = pendingValue.value;
    }
});
</script>

<template>
    <div ref="scrubber" class="relative h-2 cursor-pointer rounded bg-black/20 select-none" @mousedown="scrubbing = true">
        <div class="relative h-full w-full overflow-hidden rounded">
            <div
                class="bg-primary/30 absolute top-0 left-0 h-full w-full rounded"
                :style="{ transform: `translateX(${(secondary / max) * 100 - 100}%)` }"
            />
            <div class="bg-primary relative h-full w-full rounded" :style="{ transform: `translateX(${(model / max) * 100 - 100}%)` }" />
        </div>
        <div class="absolute inset-0 opacity-0 hover:opacity-100" :class="{ '!opacity-100': scrubbing }">
            <slot :pending-value="pendingValue" :position="`${Math.max(0, Math.min(elementX, elementWidth))}px`" />
        </div>
    </div>
</template>
