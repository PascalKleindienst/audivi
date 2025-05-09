<script setup lang="ts" generic="T extends ItemProps">
import { ItemProps } from '@/Components/Layout';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import { Skeleton } from '@/Components/ui/skeleton';
import { Table, TableBody, TableCell, TableRow } from '@/Components/ui/table';
import axios from 'axios';
import { DownloadIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    query: string;
    type: 'book' | 'author';
    providers: string[];
    defaultProvider: string;
}>();

const $emit = defineEmits<{
    'select:item': [item: { id: string | number; [key: string]: unknown }];
}>();

const isDialogOpen = ref(false);
const error = ref(null);
const locales = ['au', 'ca', 'de', 'es', 'fr', 'in', 'it', 'jp', 'us', 'uk'];
const loading = ref(false);
const data = ref();
const currentProvider = ref(props.defaultProvider);
const currentLocale = ref('de');

const fetchMetadata = async () => {
    loading.value = true;

    type MetadataResponse = {
        error?: string;
        items: T[];
    };

    axios
        .post<MetadataResponse>(route('metadata.fetch'), {
            query: props.query,
            type: props.type,
            provider: currentProvider.value,
            locale: currentLocale.value
        })
        .then((response) => {
            data.value = response.data.items;
        })
        .catch((err) => {
            error.value = err.response.data.error;
            console.error(err);
        })
        .finally(() => {
            loading.value = false;
        });
};

// Re-fetch metadata when provider or locale changes
watch([currentProvider, currentLocale], () => {
    fetchMetadata();
});

// Add a watcher to detect changes
watch(isDialogOpen, (open) => {
    if (open === true) {
        fetchMetadata();
    }
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <slot name="trigger">
                <Button>{{ $t('metadata.fetch') }}</Button>
            </slot>
        </DialogTrigger>
        <DialogContent class="max-h-[90dvh] grid-rows-[auto_minmax(0,1fr)_auto] sm:max-w-3/4">
            <DialogHeader>
                <DialogTitle>Edit profile</DialogTitle>
                <DialogDescription> Make changes to your profile here. Click save when you're done.</DialogDescription>
                <div class="flex flex-wrap justify-between gap-4">
                    <div class="flex gap-2">
                        <Button
                            v-for="provider in props.providers"
                            :key="provider"
                            size="sm"
                            :variant="currentProvider === provider ? 'default' : 'ghost'"
                            @click="currentProvider = provider"
                        >
                            {{ provider }}
                        </Button>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="locale in locales"
                            :key="locale"
                            size="icon"
                            :variant="currentLocale === locale ? 'default' : 'ghost'"
                            @click="currentLocale = locale"
                        >
                            {{ locale }}
                        </Button>
                    </div>
                </div>
            </DialogHeader>
            <div v-if="loading">
                <div class="space-y-4 divide-y-2">
                    <div v-for="i in [1, 2, 3, 4]" :key="i" class="flex w-full items-center space-x-4 p-2">
                        <Skeleton class="bg-muted-foreground size-10 rounded md:size-20" />
                        <Skeleton class="bg-muted-foreground h-4 w-3/12" />

                        <div class="flex-1 space-y-2">
                            <Skeleton class="bg-muted-foreground h-4 w-10/12" />
                            <Skeleton class="bg-muted-foreground h-4 w-8/12" />
                            <Skeleton class="bg-muted-foreground h-4 w-9/12" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="error" class="text-destructive p-4">
                <pre>{{ error }}</pre>
            </div>
            <!--<div v-else class="divide-y-2 overflow-y-auto">-->
            <Table v-else class="overflow-y-auto">
                <TableBody>
                    <TableRow v-for="_item in data" :key="_item.id">
                        <slot name="row" :item="_item" />

                        <TableCell class="text-right">
                            <Button
                                @click="
                                    $emit('select:item', _item);
                                    isDialogOpen = false;
                                "
                            >
                                <DownloadIcon />
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </DialogContent>
    </Dialog>
</template>
