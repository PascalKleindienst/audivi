<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { PaginatedDataCollection } from '@/types/inertia';
import AuthorData = App.Data.AuthorData;
import PageHeader from '@/Components/PageHeader.vue';
import { Grid, GridItem } from '@/Components/ui/grid';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps<{
    authors: PaginatedDataCollection<AuthorData>;
}>();
</script>

<template>
    <Head title="Authors" />

    <PageHeader>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Authors</h2>
    </PageHeader>

    <Grid>
        <GridItem v-for="author in props.authors.data" :key="author.id" as-child>
            <Link
                :href="route('authors.show', author.id)"
                @keydown.space.prevent="() => $inertia.visit(route('authors.show', author.id))"
            >
                <div class="space-y-2">
                    <img
                        v-if="author.image"
                        :src="author.image"
                        :alt="author.name"
                        class="w-full rounded-xl object-cover shadow-xl [aspect-ratio:1/1]"
                    />
                    <div
                        v-else
                        class="flex items-center justify-center rounded-xl bg-accent text-center font-serif text-xl font-bold shadow-xl [aspect-ratio:1/1]"
                    >
                        {{ author.name }}
                    </div>
                    <h3 class="font-serif text-xl font-bold">{{ author.name }}</h3>
                </div>
            </Link>
        </GridItem>
    </Grid>

    <Pagination :paginator="authors" :only="['authors']" />
</template>
