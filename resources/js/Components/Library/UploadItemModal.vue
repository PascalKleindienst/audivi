<script setup lang="ts">
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { route } from 'ziggy-js';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import Checkbox from '@/Components/Checkbox.vue';
import FileUploadDropzone from '@/Components/Library/FileUploadDropzone.vue';
import FileUploadProgress from '@/Components/Library/FileUploadProgress.vue';
import Progressbar from '@/Components/Progressbar.vue';
import { FileUpload, useFileUpload } from '@/Composables/fileUpload';

const open = ref(false);
const overwriteFiles = ref(false);

const { state, reset, upload, add, remove } = useFileUpload(route('audio-books.store'));

const onSelectedFiles = (files: FileList) => {
    reset();
    add(files);
};
</script>

<template>
    <Dialog v-model:open="open" @update:open="() => reset()">
        <DialogTrigger as-child>
            <Button>{{ trans('library.add_files') }}</Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ trans('library.upload.title') }}</DialogTitle>
                <DialogDescription>
                    <div v-if="state.files.length">
                        <div v-for="selectedFile in state.files" :key="selectedFile.file.name" class="mb-6">
                            <FileUploadProgress
                                :file-upload="selectedFile"
                                @remove="remove(selectedFile)"
                                @upload="upload(selectedFile, overwriteFiles)"
                            />

                            <Progressbar
                                v-if="selectedFile.uploading"
                                :progress="selectedFile.process"
                                :error="!!selectedFile.error"
                            />

                            <div v-show="selectedFile.error">
                                <p class="text-sm text-destructive">
                                    {{ selectedFile.error }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <FileUploadDropzone v-if="!state.files.length || state.hasError" @selected-files="onSelectedFiles">
                        {{ trans('library.upload.dropzone') }}
                    </FileUploadDropzone>

                    <div v-if="!state.uploading || state.hasError" class="items-top mt-4 flex gap-x-2">
                        <Checkbox id="overwrite" v-model:checked="overwriteFiles" />
                        <Label for="overwrite">{{ trans('library.upload.overwrite') }}</Label>
                    </div>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <p class="mr-auto flex gap-x-2 py-2 text-sm font-bold">
                    <span v-if="state.waiting">
                        {{ trans('library.upload.items.left', { count: state.waiting.toString() }) }}
                    </span>
                    <span v-if="state.successful">
                        {{ trans('library.upload.items.successful', { count: state.successful.toString() }) }}
                    </span>
                    <span v-if="state.failed">
                        {{ trans('library.upload.items.failed', { count: state.failed.toString() }) }}
                    </span>
                </p>
                <DialogClose as-child>
                    <Button variant="secondary"> {{ trans('general.cancel') }} </Button>
                </DialogClose>

                <Button
                    class="ms-3"
                    :class="{ 'opacity-25': state.files.length === 0 || state.uploading }"
                    :disabled="state.files.length === 0 || state.uploading"
                    @click="state.files.forEach((file: FileUpload) => upload(file, overwriteFiles))"
                >
                    {{ trans('library.upload.all') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
