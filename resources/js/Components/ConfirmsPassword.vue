<script setup lang="ts">
import { nextTick, reactive, ref } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { route } from 'ziggy-js';
import InputError from './InputError.vue';
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

const emit = defineEmits(['confirmed']);

defineProps({
    title: {
        type: String,
        default: 'auth.confirm_password'
    },
    button: {
        type: String,
        default: 'general.confirm'
    }
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false
});

const passwordInput = ref<InstanceType<typeof Input> | null>(null);

const startConfirmingPassword = () => {
    confirmingPassword.value = false;

    window.axios.get(route('password.confirmation')).then((response) => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    window.axios
        .post(route('password.confirm'), {
            password: form.password
        })
        .then(() => {
            form.processing = false;

            closeModal();
            nextTick().then(() => emit('confirmed'));
        })
        .catch((error) => {
            form.processing = false;
            form.error = error.response.data.errors.password[0];
            (passwordInput.value?.$el as HTMLInputElement).focus();
        });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
    <Dialog v-model:open="confirmingPassword">
        <DialogTrigger as-child @click="startConfirmingPassword">
            <slot name="trigger" :form="form" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ trans(title) }}</DialogTitle>
                <DialogDescription>
                    <slot> {{ trans('auth.confirm_password.subtitle') }} </slot>
                    <div class="mt-4">
                        <Input
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            :placeholder="trans('auth.password')"
                            autocomplete="current-password"
                            @keyup.enter="confirmPassword"
                        />

                        <InputError :message="form.error" class="mt-2" />
                    </div>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="secondary"> {{ trans('general.cancel') }} </Button>
                </DialogClose>

                <Button
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                    {{ trans(button) }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
