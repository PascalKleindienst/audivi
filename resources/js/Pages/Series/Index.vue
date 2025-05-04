<script setup lang="ts">
import { AudiobookCover } from '@/Components/Audiobooks';
import { ListingLayout } from '@/Components/Layout';
import { PaginatedDataCollection } from '@/types/inertia';
import { Head } from '@inertiajs/vue3';
import SeriesData = App.Data.SeriesData;
import AudioBookData = App.Data.AudioBookData;

const props = defineProps<{
    series: PaginatedDataCollection<SeriesData>;
}>();

const covers = (books: AudioBookData[] | null) => books?.map((book: AudioBookData) => book.cover).filter((src) => src !== null);
</script>

<template>
    <Head :title="$t('series.title')" />

    <ListingLayout :model-value="props.series.data" route-key="series.show">
        <template #grid="{ item }: { item: SeriesData }">
            <div class="flex h-full flex-col space-y-4">
                <div v-if="item.books?.length" class="grid grid-cols-2 gap-2">
                    <div v-for="cover in covers(item.books)" :key="cover">
                        <AudiobookCover :title="item.name" :cover="cover" :pattern="item.id" />
                    </div>
                </div>
                <h3 class="mt-auto line-clamp-2 h-[2lh] font-serif text-xl font-bold">{{ item.name }}</h3>
            </div>
        </template>

        <template #list="{ item }: { item: SeriesData }">
            <div class="flex h-full gap-4 space-y-4">
                <div v-if="item.books?.length" class="grid w-25 grid-cols-2 gap-1">
                    <div v-for="cover in covers(item.books)" :key="cover">
                        <AudiobookCover :title="item.name" :cover="cover" :pattern="item.id" />
                    </div>
                </div>
                <h3 class="font-serif text-xl/8 font-bold">{{ item.name }}</h3>
            </div>
        </template>
    </ListingLayout>
</template>
