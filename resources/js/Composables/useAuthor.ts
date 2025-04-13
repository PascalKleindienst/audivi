import { usePhoto } from '@/Composables/photo';
import { useForm } from '@inertiajs/vue3';
import { Ref, ref } from 'vue';
import { route } from 'ziggy-js';
import AuthorData = App.Data.AuthorData;

export function useAuthor(imageInput: Ref<HTMLInputElement>, author: AuthorData) {
    const imagePreview = ref<string | ArrayBuffer | null>(null);

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
        if (files && files.length) {
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
