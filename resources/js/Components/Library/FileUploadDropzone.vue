<script setup lang="ts">
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { Label } from '@/Components/ui/label';
import { trans } from 'laravel-vue-i18n';

const emits = defineEmits<{
    (e: 'selectedFiles', files: FileList): void;
}>();

const fileUpload = ref<HTMLInputElement>();
const dropzoneActive = ref(false);

const onDrop = (e: DragEvent) => {
    const files = e.dataTransfer?.files;
    dropzoneActive.value = false;
    if (files?.length) {
        emits('selectedFiles', files);
    }
};

const onSelectFile = () => {
    const files = fileUpload.value?.files;
    if (files?.length) {
        emits('selectedFiles', files);
    }
};
</script>

<template>
    <div
        :class="{ 'border-2 border-primary': dropzoneActive }"
        class="relative flex min-h-40 flex-col items-center justify-center rounded border border-dashed border-border p-12"
    >
        <div
            :class="{ 'bg-primary/10': dropzoneActive }"
            class="absolute flex h-full w-full cursor-pointer"
            @dragenter.prevent="dropzoneActive = true"
            @dragleave.prevent="dropzoneActive = false"
            @dragover.prevent="dropzoneActive = true"
            @drop.prevent="onDrop"
            @click="fileUpload?.click()"
        ></div>

        <Icon class="size-12" icon="heroicons:arrow-up-on-square-stack" />
        <div class="mt-4">
            {{ trans('general.upload.drop') }}
            <Label for="file-upload" class="cursor-pointer text-primary">{{ trans('general.upload.browse') }}</Label>
            <input
                id="file-upload"
                ref="fileUpload"
                type="file"
                class="hidden"
                accept="application/zip,.mp3,audio/*"
                multiple
                @change="onSelectFile"
            />
        </div>
        <p class="mt-2 text-xs">
            <slot />
        </p>
    </div>
</template>
