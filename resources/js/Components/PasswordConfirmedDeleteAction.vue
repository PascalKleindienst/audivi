<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
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

const open = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);
const props = defineProps<{ route: string; title: string }>();
const form = useForm({
    password: ''
});

const deleteAction = () => {
    form.delete(props.route, {
        preserveScroll: true,
        onSuccess: () => (open.value = false),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset()
    });
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="destructive"> {{ title }} </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>
                    <slot />

                    <div class="mt-4">
                        <Input
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            :placeholder="trans('auth.password')"
                            autocomplete="current-password"
                            @keyup.enter="deleteAction"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="secondary"> {{ trans('general.cancel') }} </Button>
                </DialogClose>

                <Button
                    variant="destructive"
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteAction"
                >
                    {{ title }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
