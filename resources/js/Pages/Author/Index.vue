<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import { Button } from '@/Components/ui/button';
import { Grid, GridItem } from '@/Components/ui/grid';
import { PaginatedDataCollection } from '@/types/inertia';
import { Head, Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { LayoutGridIcon, ListIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import AuthorData = App.Data.AuthorData;

const props = defineProps<{
    authors: PaginatedDataCollection<AuthorData>;
}>();

const layout = ref('grid');
</script>

<template>
    <Head :title="trans('author.title')" />

    <div class="p-4">
        <Button size="icon" :variant="layout === 'list' ? 'default' : 'secondary'" class="rounded-tr-none rounded-br-none" @click="layout = 'list'">
            <ListIcon />
        </Button>
        <Button size="icon" :variant="layout === 'grid' ? 'default' : 'secondary'" class="rounded-tl-none rounded-bl-none" @click="layout = 'grid'">
            <LayoutGridIcon />
        </Button>
    </div>

    <Grid v-if="layout === 'grid'" data-testid="author-grid" class="p-4">
        <GridItem v-for="author in props.authors.data" :key="author.id" as-child>
            <Link :href="route('authors.show', author.id)" @keydown.space.prevent="() => $inertia.visit(route('authors.show', author.id))">
                <div class="space-y-4">
                    <img
                        v-if="author.image"
                        :src="author.image"
                        :alt="author.name"
                        class="[aspect-ratio:1/1] w-full rounded-xl object-cover shadow-xl"
                    />
                    <div
                        v-else
                        class="bg-muted-foreground text-muted flex [aspect-ratio:1/1] items-center justify-center rounded-xl text-center font-serif text-xl font-bold shadow-xl"
                    >
                        {{ author.name }}
                    </div>
                    <h3 class="font-serif text-xl [line-height:1] font-bold">{{ author.name }}</h3>
                </div>
            </Link>
        </GridItem>
    </Grid>
    <Grid v-else class="[grid-template-columns:repeat(auto-fill,_minmax(360px,_1fr))] p-4" data-testid="author-grid">
        <GridItem v-for="author in props.authors.data" :key="author.id" as-child>
            <Link :href="route('authors.show', author.id)" @keydown.space.prevent="() => $inertia.visit(route('authors.show', author.id))">
                <div class="flex gap-4 space-y-4">
                    <img
                        v-if="author.image"
                        :src="author.image"
                        :alt="author.name"
                        class="[aspect-ratio:1/1] w-25 rounded-xl object-cover shadow-xl"
                    />
                    <div
                        v-else
                        class="bg-muted-foreground text-muted flex [aspect-ratio:1/1] w-25 items-center justify-center rounded-xl p-2 text-center font-serif text-sm"
                    >
                        {{ author.name }}
                    </div>
                    <h3 class="font-serif text-xl/8 font-bold">{{ author.name }}</h3>
                </div>
            </Link>
        </GridItem>
    </Grid>

    <Pagination :paginator="authors" :only="['authors']" data-testid="author-pagination" />
</template>
