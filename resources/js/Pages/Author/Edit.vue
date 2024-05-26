<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthorData = App.Data.AuthorData;
import PageHeader from '@/Components/PageHeader.vue';
import { route } from 'ziggy-js';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { trans } from 'laravel-vue-i18n';
import { Textarea } from '@/Components/ui/textarea';
import { Button } from '@/Components/ui/button';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { usePhoto } from '@/Composables/photo';

const props = defineProps<{
    author: AuthorData;
}>();

const form = useForm({
    _method: 'PUT',
    name: props.author.name,
    link: props.author.link,
    description: props.author.description,
    image: null as File | null
});

const imagePreview = ref<string | ArrayBuffer | null>(null);
const imageInput = ref<HTMLInputElement | null>(null);
const { updatePreview, selectNewPhoto, clearFileInput } = usePhoto(imageInput, imagePreview);

const submit = () => {
    const files = imageInput.value?.files;
    if (files && files.length) {
        form.image = files[0] ?? null;
    }

    form.post(route('authors.update', props.author.id), {
        preserveScroll: true,
        onSuccess: () => clearFileInput()
    });
};
</script>

<template>
    <Head :title="props.author.name" />

    <div class="mx-auto max-w-screen-xl px-3 py-2 sm:px-6 lg:px-8">
        <PageHeader :title="props.author.name" :description="props.author.description" />
        <form class="grid grid-cols-12 gap-6" @submit.prevent="submit">
            <div class="col-span-12 grid place-items-center md:col-span-4">
                <input id="image" ref="imageInput" type="file" class="hidden" @change="updatePreview" />
                <Label for="image">{{ trans('author.image') }}</Label>

                <div v-show="!imagePreview" class="mt-2">
                    <img
                        :src="props.author.image"
                        :alt="props.author.name"
                        class="size-40 rounded-full object-cover text-center"
                    />
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

            <footer class="col-span-12 space-x-2">
                <Button variant="default" type="submit">{{ trans('general.save') }}</Button>

                <Link :href="route('authors.show', author.id)" as-child>
                    <Button variant="secondary">{{ trans('general.cancel') }}</Button>
                </Link>
            </footer>
        </form>
    </div>
</template>
