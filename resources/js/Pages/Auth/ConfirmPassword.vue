<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    password: ''
});

const passwordInput = ref<HTMLInputElement | null>(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value?.focus();
        }
    });
};
</script>

<template>
    <Head :title="$t('Confirm Password')" />

    <AuthLayout
        :title="$t('Confirm Password')"
        :description="$t('This is a secure area of the application. Please confirm your password before continuing.')"
    >
        <form @submit.prevent="submit">
            <div>
                <Label for="password">{{ $t('Password') }}</Label>
                <Input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('Confirm') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
