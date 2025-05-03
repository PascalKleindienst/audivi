<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { useAuthor } from '@/Composables/useAuthor';
import { Head, Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useTemplateRef } from 'vue';
import AuthorData = App.Data.AuthorData;

const { author } = defineProps<{
    author: AuthorData;
}>();

const imageRef = useTemplateRef('imageInput');
const { form, submit, imagePreview, updatePreview, selectNewPhoto } = useAuthor(imageRef, author);
</script>

<template>
    <Head :title="author.name" />

    <form class="mx-auto w-full max-w-6xl p-4" @submit.prevent="submit">
        <Card>
            <CardContent class="grid grid-cols-12 gap-6">
                <div class="col-span-12 grid place-items-center md:col-span-4">
                    <input id="image" ref="imageInput" type="file" class="hidden" @change="updatePreview" />
                    <Label for="image">{{ trans('author.image') }}</Label>

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
                        {{ trans('author.image.select') }}
                    </Button>

                    <InputError :message="form.errors.image" class="mt-2" />
                </div>
                <div class="col-span-12 space-y-6 md:col-span-8">
                    <div class="grid gap-2">
                        <Label for="name">{{ trans('author.name') }}</Label>
                        <Input id="name" v-model="form.name" type="text" name="name" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">{{ trans('author.description') }}</Label>
                        <Textarea id="description" v-model="form.description" name="description" />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="link">{{ trans('author.link') }}</Label>
                        <Input id="link" v-model="form.link" type="text" name="link" />
                        <InputError :message="form.errors.link" class="mt-2" />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-between">
                <Button variant="default" type="submit">{{ trans('general.save') }}</Button>
                <Button :as="Link" variant="secondary" :href="route('authors.show', author.id)">
                    {{ trans('general.cancel') }}
                </Button>
            </CardFooter>
        </Card>
    </form>
</template>
