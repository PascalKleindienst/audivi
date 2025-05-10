<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
import SectionTitle from './SectionTitle.vue';
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
            <div class="max-w-4xl">
                <CardContent class="space-y-4">
                    <slot name="content" />
                </CardContent>
            </div>
        </div>
    </Card>
</template>
