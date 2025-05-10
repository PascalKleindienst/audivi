<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev
} from '@/Components/ui/pagination';
import { PaginatedDataCollection } from '@/types/inertia';
import { router } from '@inertiajs/vue3';

const props = defineProps<{ paginator: PaginatedDataCollection<unknown>; only?: string[] }>();

const options: { only?: string[] } = {};

if (props.only) {
    options.only = props.only;
}
</script>

<template>
    <Pagination
        v-if="paginator.meta.last_page > 1"
        v-slot="{ page }"
        :total="paginator.meta.total"
        :items-per-page="paginator.meta.per_page"
        :sibling-count="1"
        show-edges
        :default-page="paginator.meta.current_page"
        class="mx-auto mt-8"
        @update:page="
            (currentPage) => {
                let url = paginator.links[currentPage]?.url;
                if (url) {
                    router.visit(url, options);
                }
            }
        "
    >
        <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
            <PaginationFirst />
            <PaginationPrev />

            <template v-for="(item, index) in items">
                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                    <Button class="h-10 w-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                        {{ item.value }}
                    </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>

            <PaginationNext />
            <PaginationLast />
        </PaginationList>
    </Pagination>
</template>
