import { usePhoto } from '@/Composables/photo';
import { useForm } from '@inertiajs/vue3';
import { MaybeElementRef } from '@vueuse/core';
import { ref, toRef } from 'vue';
import { route } from 'ziggy-js';
import AuthorData = App.Data.AuthorData;

export function useAuthor(imageInput: MaybeElementRef<HTMLInputElement | null>, author: AuthorData) {
    const imagePreview = ref<string | ArrayBuffer | null>(null);
    imageInput = toRef(imageInput);

    const form = useForm({
        _method: 'PUT',
        name: author.name,
        link: author.link,
        description: author.description,
        image: null as File | null
    });

    const { updatePreview, selectNewPhoto, clearFileInput } = usePhoto(imageInput, imagePreview);

    const submit = () => {
        const files = imageInput.value?.files;
        if (files?.length) {
            form.image = files[0] ?? null;
        }

        form.post(route('authors.update', author.id), {
            preserveScroll: true,
            onSuccess: () => clearFileInput()
        });
    };

    return {
        imagePreview,
        updatePreview,
        selectNewPhoto,
        submit,
        form
    };
}
