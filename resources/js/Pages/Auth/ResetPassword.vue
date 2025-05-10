<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { AuthLayout, type AuthLayoutProps, useLayout } from '@/Layouts';
import { Head, useForm } from '@inertiajs/vue3';

const props = withDefaults(defineProps<{ email: string; token: string }>(), {
    email: '',
    token: ''
});

defineOptions(
    useLayout<AuthLayoutProps>(AuthLayout, {
        title: 'Reset Password'
    })
);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation')
    });
};
</script>

<template>
    <Head :title="$t('Reset Password')" />

    <form @submit.prevent="submit">
        <div>
            <Label for="email">{{ $t('Email') }}</Label>
            <Input id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <Label for="password">{{ $t('Password') }}</Label>
            <Input id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <Label for="password_confirmation"> {{ $t('Confirm Password') }} </Label>
            <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                required
                autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> {{ $t('Reset Password') }}</Button>
        </div>
    </form>
</template>
