<script setup lang="ts">
import SeriesData = App.Data.SeriesData;
import AudioBookData = App.Data.AudioBookData;
import { AudiobookCard } from '@/Components/Audiobooks';
import { ListingLayout } from '@/Components/Layout';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    series: SeriesData;
}>();
</script>

<template>
    <Head :title="props.series.name" />

    <ListingLayout v-if="props.series.books" :model-value="props.series.books" route-key="audio-books.show">
        <template #grid="{ item }: { item: AudioBookData }">
            <AudiobookCard :item="item" type="grid" />
        </template>

        <template #list="{ item }: { item: AudioBookData }">
            <AudiobookCard :item="item" type="list" />
        </template>
    </ListingLayout>
</template>
