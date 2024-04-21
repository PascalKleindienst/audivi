<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { trans } from 'laravel-vue-i18n';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { toast } from '@/Components/ui/toast';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        }
    });
};

watchEffect(() => {
    if (form.recentlySuccessful) {
        toast({
            variant: 'success',
            description: trans('general.saved_success')
        });
    }
});
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>{{ trans('profile.password.title') }}</template>

        <template #description>{{ trans('profile.password.description') }}</template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <Label for="current_password">{{ trans('profile.password.current') }}</Label>
                <Input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <Label for="password">{{ trans('profile.password.new') }}</Label>
                <Input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <Label for="password_confirmation">{{ trans('profile.password.confirm') }}</Label>
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ trans('general.save') }}
            </Button>
        </template>
    </FormSection>
</template>
