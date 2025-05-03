<script setup lang="ts">
import AudiobookCover from '@/Components/Audiobooks/AudiobookCover.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Grid, GridItem } from '@/Components/ui/grid';
import { useHumanTime } from '@/Composables/human';
import { Icon } from '@iconify/vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { MicIcon, PencilLineIcon } from 'lucide-vue-next';
import AuthorData = App.Data.AuthorData;
import AudioBookData = App.Data.AudioBookData;

const props = defineProps<{
    author: AuthorData;
    books: AudioBookData[];
}>();
</script>

<template>
    <Head :title="props.author.name" />

    <div class="p-4">
        <Card class="container mx-auto">
            <CardContent class="grid grid-cols-12 gap-6">
                <div v-if="props.author.image" class="col-span-12 mx-auto md:col-span-4">
                    <img :src="props.author.image" :alt="props.author.name" class="size-64 rounded-full object-cover text-center" />
                </div>

                <div class="col-span-12 space-y-6 md:col-span-8">
                    <PageHeader :title="props.author.name">
                        <template #actions>
                            <Button :as="Link" :href="route('authors.edit', props.author.id)" :title="trans('general.edit')">
                                <PencilLineIcon class="size-5" />
                            </Button>
                        </template>
                    </PageHeader>

                    <div v-if="props.author.description" class="prose">
                        {{ props.author.description }}
                    </div>
                </div>
            </CardContent>
        </Card>

        <div v-if="books.length" class="mt-10">
            <header class="mb-6 flex items-center justify-between">
                <h2 class="block text-2xl font-bold">{{ trans('general.most_popular') }}</h2>
            </header>

            <Grid>
                <GridItem v-for="book in props.books" :key="book.id" as-child>
                    <Link :href="route('audio-books.show', book.id)" @keydown.space.prevent="() => router.visit(route('audio-books.show', book.id))">
                        <AudiobookCover :title="book.title" :cover="book.cover" :pattern="book.id" class="bg-cover" />
                        <div class="flex gap-2">
                            <div class="mb-1 flex flex-1 flex-col">
                                <h3 class="font-serif text-lg font-bold">{{ book.title }}</h3>
                                <h5 v-if="book.subtitle" class="font-serif font-bold">{{ book.subtitle }}</h5>
                                <div class="mt-auto flex items-center justify-between">
                                    <div v-if="book.duration" class="space-x-1">
                                        <MicIcon class="inline size-5" />
                                        <span class="text-sm tracking-wide">{{ useHumanTime(book.duration) }}</span>
                                    </div>

                                    <div v-if="book.rating" class="space-x-1">
                                        <Icon icon="heroicons:star" class="inline size-5" />

                                        <span class="text-sm tracking-wide">{{ book.rating }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </GridItem>
            </Grid>
        </div>
    </div>
</template>
