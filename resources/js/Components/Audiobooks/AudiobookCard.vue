<script setup lang="ts">
import { AudiobookCover, AudiobookMeta } from '@/Components/Audiobooks/index';
import type { LayoutType } from '@/Components/Layout';
import AudioBookData = App.Data.AudioBookData;

const props = withDefaults(
    defineProps<{
        item: AudioBookData;
        type: LayoutType;
    }>(),
    {
        type: 'grid'
    }
);
</script>

<template>
    <div v-if="props.type === 'grid'" class="space-y-4" :title="item.title">
        <div class="space-y-4">
            <AudiobookCover :title="item.title" :cover="item.cover" :pattern="item.id" />
            <div class="min-h-[3lh] space-y-2">
                <h3 class="line-clamp-2 font-serif text-xl font-bold">{{ item.title }}</h3>
                <h4 v-if="item.subtitle" class="text-md line-clamp-1 h-[1lh] font-medium">{{ item.subtitle }}</h4>
            </div>
        </div>
    </div>
    <div v-else class="flex gap-4 space-y-4">
        <AudiobookCover :title="item.title" :cover="item.cover" :pattern="item.id" class="size-25 object-center" />
        <div class="space-y-2">
            <div class="min-h-[3lh] space-y-2">
                <h3 class="line-clamp-2 font-serif text-xl/6 font-bold">{{ item.title }}</h3>
                <h4 v-if="item.subtitle" class="text-md line-clamp-1 h-[1lh] font-medium">{{ item.subtitle }}</h4>
            </div>

            <AudiobookMeta :item="item" class="text-sm" />
        </div>
    </div>
</template>
