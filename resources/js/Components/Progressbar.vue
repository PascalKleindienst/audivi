<script setup lang="ts">
interface Props {
    progress: number;
    min?: number;
    max?: number;
    error?: boolean;
}

withDefaults(defineProps<Props>(), {
    min: 0,
    max: 100
});
</script>

<template>
    <div class="flex items-center gap-x-3 whitespace-nowrap">
        <div
            class="flex h-2 w-full overflow-hidden rounded-full bg-accent"
            role="progressbar"
            :aria-valuenow="progress"
            :aria-valuemin="min"
            :aria-valuemax="max"
        >
            <div
                :class="{
                    'bg-destructive': error,
                    'bg-primary': !error
                }"
                class="flex flex-col justify-center overflow-hidden whitespace-nowrap rounded-full text-center text-xs transition-all duration-500"
                :style="`width: ${progress}%`"
            ></div>
        </div>
        <div class="w-12 text-end">
            <span class="text-sm">{{ progress }}%</span>
        </div>
    </div>
</template>
