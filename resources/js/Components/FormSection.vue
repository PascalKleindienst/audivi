<script setup lang="ts">
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
    <Card>
        <CardHeader>
            <SectionTitle>
                <template #title>
                    <slot name="title" />
                </template>
                <template #description>
                    <slot name="description" />
                </template>
            </SectionTitle>
        </CardHeader>

        <div class="mt-5 md:col-span-2 md:mt-0">
            <form class="max-w-4xl" @submit.prevent="$emit('submitted')">
                <CardContent class="space-y-4">
                    <slot name="form" />
                </CardContent>

                <CardFooter v-if="hasActions" class="flex items-center justify-end text-end">
                    <slot name="actions" />
                </CardFooter>
            </form>
        </div>
    </Card>
</template>
