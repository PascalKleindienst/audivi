<script setup lang="ts">
defineProps<{
    on: boolean;
}>();
</script>

<template>
    <div>
        <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="on" class="text-success text-sm">
                <slot />
            </div>
        </transition>
    </div>
</template>
