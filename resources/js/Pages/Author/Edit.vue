<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { ItemProps } from '@/Components/Layout';
import { MetadataModal } from '@/Components/Metadata';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { TableCell } from '@/Components/ui/table';
import { Textarea } from '@/Components/ui/textarea';
import { useAuthor } from '@/Composables/useAuthor';
import { Head, Link } from '@inertiajs/vue3';
import { UserIcon } from 'lucide-vue-next';
import { useTemplateRef } from 'vue';
import AuthorData = App.Data.AuthorData;

const props = defineProps<{
    author: AuthorData;
    providers: string[];
    defaultProvider: string;
}>();

const imageRef = useTemplateRef('imageInput');
const { form, submit, imagePreview, updatePreview, selectNewPhoto } = useAuthor(imageRef, props.author);

const onSelectAuthor = async (item: ItemProps) => {
    const author = item as AuthorData;

    // Turn Image into blob
    if (author.image) {
        const response = await fetch(author.image);
        const blob = await response.blob();
        const file = new File([blob], 'image.jpg', { type: blob.type });

        imagePreview.value = author.image;
        form.image = file;
    }

    form.name = author.name;
    form.description = author.description;
    form.link = author.link;
};
</script>

<template>
    <Head :title="author.name" />

    <form class="mx-auto w-full max-w-6xl p-4" @submit.prevent="submit">
        <Card>
            <CardContent class="grid grid-cols-12 gap-6">
                <div class="col-span-12 grid place-items-center md:col-span-4">
                    <input id="image" ref="imageInput" type="file" class="hidden" @change="updatePreview" />
                    <Label for="image">{{ $t('author.image') }}</Label>

                    <div v-show="!imagePreview" class="mt-2">
                        <img :src="author.image" :alt="author.name" class="size-40 rounded-full object-cover text-center" />
                    </div>

                    <div v-show="imagePreview" class="mt-2">
                        <span
                            class="block size-40 rounded-full bg-cover bg-center bg-no-repeat"
                            :style="'background-image: url(\'' + imagePreview + '\');'"
                        />
                    </div>

                    <Button variant="secondary" class="me-2 mt-2" type="button" @click.prevent="selectNewPhoto">
                        {{ $t('author.image.select') }}
                    </Button>

                    <InputError :message="form.errors.image" class="mt-2" />
                </div>
                <div class="col-span-12 space-y-6 md:col-span-8">
                    <div class="grid gap-2">
                        <Label for="name">{{ $t('author.name') }}</Label>
                        <Input id="name" v-model="form.name" type="text" name="name" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">{{ $t('author.description') }}</Label>
                        <Textarea id="description" v-model="form.description" name="description" />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="link">{{ $t('author.link') }}</Label>
                        <Input id="link" v-model="form.link" type="text" name="link" />
                        <InputError :message="form.errors.link" class="mt-2" />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-between">
                <div class="space-x-2">
                    <Button variant="default" type="submit">{{ $t('general.save') }}</Button>

                    <MetadataModal
                        :query="author.name"
                        type="author"
                        :providers="providers"
                        :default-provider="defaultProvider"
                        @select:item="onSelectAuthor"
                    >
                        <template #trigger>
                            <Button variant="ghost" type="button"> {{ $t('metadata.fetch') }}</Button>
                        </template>

                        <template #row="{ item }: { item: AuthorData }">
                            <TableCell class="w-12 md:w-24">
                                <img v-if="item.image" :src="item.image" :alt="item.name" class="rounded object-cover" />
                                <div v-else>
                                    <UserIcon class="bg-muted text-muted-foreground rounded object-cover" />
                                </div>
                            </TableCell>
                            <TableCell class="space-y-2 md:min-w-48">
                                <div>{{ item.name }}</div>
                                <p class="text-muted-foreground line-clamp-2 whitespace-normal italic md:hidden">{{ item.description }}</p>
                            </TableCell>
                            <TableCell class="hidden md:table-cell">
                                <p class="text-muted-foreground line-clamp-4 whitespace-normal italic">{{ item.description }}</p>
                            </TableCell>
                        </template>
                    </MetadataModal>
                </div>
                <Button :as="Link" variant="secondary" :href="route('authors.show', author.id)" prefetch>
                    {{ $t('general.cancel') }}
                </Button>
            </CardFooter>
        </Card>
    </form>
</template>
