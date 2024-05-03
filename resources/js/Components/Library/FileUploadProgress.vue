<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Button } from '@/Components/ui/button';
import { FileUpload } from '@/Composables/fileUpload';

defineProps<{ fileUpload: FileUpload }>();
defineEmits<{
    (e: 'remove', fileUpload: FileUpload): void;
    (e: 'upload', fileUpload: FileUpload): void;
}>();
</script>

<template>
    <div class="mb-2 flex items-center justify-between">
        <div class="flex items-center gap-x-3">
            <span class="flex items-center justify-center rounded-lg border border-border p-2">
                <Icon icon="heroicons:arrow-up-on-square-stack" class="size-6" />
            </span>
            <div>
                <p class="text-sm font-bold" :class="{ 'text-destructive': fileUpload.error }">
                    {{ fileUpload.file.name }}
                </p>
                <p class="text-xs">{{ fileUpload.fileSize }}</p>
            </div>
        </div>
        <div class="inline-flex items-center gap-x-2">
            <svg
                v-show="fileUpload.uploading && !fileUpload.error && !fileUpload.complete"
                class="size-5 animate-spin text-primary"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <Icon v-show="fileUpload.complete" icon="heroicons:check-circle" class="size-5 text-success"></Icon>
            <Icon v-show="fileUpload.error" icon="heroicons:exclamation-triangle" class="size-5 text-destructive" />
            <Button
                v-show="!fileUpload.uploading"
                variant="ghost"
                aria-label="Upload"
                @click="$emit('upload', fileUpload)"
            >
                <Icon icon="heroicons:arrow-up-tray" class="size-5" />
            </Button>
            <Button
                v-show="!fileUpload.uploading || fileUpload.error"
                aria-label="Remove"
                variant="ghost"
                @click="$emit('remove', fileUpload)"
            >
                <Icon icon="heroicons:trash" class="size-5" />
            </Button>
        </div>
    </div>
</template>
