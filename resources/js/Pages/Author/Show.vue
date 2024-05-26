<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthorData = App.Data.AuthorData;
import AudioBookData = App.Data.AudioBookData;
import PageHeader from '@/Components/PageHeader.vue';
import { Grid, GridItem } from '@/Components/ui/grid';
import { route } from 'ziggy-js';
import { Icon } from '@iconify/vue';
import { Button } from '@/Components/ui/button';
import { trans } from 'laravel-vue-i18n';

defineProps<{
    author: AuthorData;
    books: AudioBookData[];
}>();
</script>

<template>
    <Head :title="author.name" />

    <div class="mx-auto max-w-screen-xl px-3 py-2 sm:px-6 lg:px-8">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-4">
                <img :src="author.image" :alt="author.name" class="size-64 rounded-full object-cover text-center" />
            </div>

            <div class="col-span-12 space-y-6 md:col-span-8">
                <PageHeader :title="author.name" :description="author.description">
                    <template #actions>
                        <Link :href="route('authors.edit', author.id)" as-child>
                            <Button variant="secondary" :title="trans('general.edit')">
                                <Icon icon="heroicons:pencil-square" class="size-5" />
                            </Button>
                        </Link>
                    </template>
                </PageHeader>

                <div v-if="author.description" class="prose">
                    {{ author.description }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10">
        <header class="mb-6 flex items-center justify-between">
            <h2 class="block text-2xl font-bold">{{ trans('general.most_popular') }}</h2>
        </header>

        <Grid>
            <GridItem v-for="book in books" :key="book.id" as-child>
                <Link
                    :href="route('audio-books.show', book.id)"
                    @keydown.space.prevent="() => $inertia.visit(route('audio-books.show', book.id))"
                >
                    <div class="flex gap-2">
                        <img
                            v-if="book.cover"
                            :src="book.cover"
                            :alt="book.title"
                            class="size-32 rounded-xl shadow-xl"
                        />
                        <div class="mb-1 flex flex-1 flex-col">
                            <h3 class="font-serif text-lg font-bold">{{ book.title }}</h3>
                            <h5 v-if="book.subtitle" class="font-serif font-bold">{{ book.subtitle }}</h5>
                            <div class="mt-auto flex items-center justify-between">
                                <!-- TODO: Duration -->
                                <!--<div class="space-x-1">-->
                                <!--    <Icon icon="heroicons:microphone" class="inline size-5" />-->
                                <!--    <span class="text-sm tracking-wide">3h 32min</span>-->
                                <!--</div>-->

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
</template>
