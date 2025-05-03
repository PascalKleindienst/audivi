import { router } from '@inertiajs/vue3';
import { Ref } from 'vue';
import { route } from 'ziggy-js';

/**
 * Returns an object containing functions to update the preview of a photo, delete the current user's photo,
 * clear the file input, and select a new photo.
 *
 * @param {Ref<HTMLInputElement | null>} photoInput - A reference to the photo input element.
 * @param {Ref<string | ArrayBuffer | null>} photoPreview - A reference to the photo preview value.
 */
export function usePhoto(photoInput: Ref<HTMLInputElement | null>, photoPreview: Ref<string | ArrayBuffer | null>) {
    /**
     * Updates the preview of a photo by reading the selected file and setting the preview value.
     */
    const updatePreview = (): void => {
        const files = photoInput.value?.files;
        const photo = files?.length ? files[0] : null;

        if (!photo) {
            return;
        }

        const reader = new FileReader();

        reader.onload = (e) => {
            photoPreview.value = e.target?.result ?? null;
        };

        reader.readAsDataURL(photo);
    };

    /**
     * Deletes the current user's photo by sending a DELETE request to the 'current-user-photo.destroy' route.
     */
    const deletePhoto = (): void => {
        router.delete(route('current-user-photo.destroy'), {
            preserveScroll: true,
            onSuccess: () => {
                photoPreview.value = null;
                clearFileInput();
            }
        });
    };

    /**
     * Clears the value of the file input if it has a value.
     */
    const clearFileInput = (): void => {
        if (photoInput.value?.value) {
            photoInput.value.value = '';
        }
    };

    /**
     * Selects a new photo by triggering a click event on the photo input element.
     */
    const selectNewPhoto = (): void => {
        photoInput.value?.click();
    };

    return { updatePreview, deletePhoto, clearFileInput, selectNewPhoto };
}
