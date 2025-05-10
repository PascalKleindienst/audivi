<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { AuthLayout, type AuthLayoutProps, useLayout } from '@/Layouts';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword: boolean;
    status?: string;
}>();

defineOptions(
    useLayout<AuthLayoutProps>(AuthLayout, {
        title: 'Login'
    })
);

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? 'on' : ''
    })).post(route('login'), {
        onFinish: () => form.reset('password')
    });
};
</script>

<template>
    <Head :title="$t('Login')" />

    <form @submit.prevent="submit">
        <div class="grid gap-4">
            <!-- TODO: Component?-->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                {{ status }}
            </div>
            <div class="grid gap-2">
                <Label for="email">{{ $t('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" placeholder="m@example.com" required autofocus autocomplete="email" />
                <InputError :message="form.errors.email" />
            </div>
            <div class="grid gap-2">
                <div class="flex items-center">
                    <Label for="password">{{ $t('Password') }}</Label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="ml-auto inline-block text-sm underline">
                        {{ $t('Forgot your password?') }}
                    </Link>
                </div>
                <Input id="password" v-model="form.password" type="password" autocomplete="current-password" required />
                <InputError :message="form.errors.password" />
            </div>
            <div class="items-top flex gap-x-2">
                <Checkbox id="remember" v-model:checked="form.remember" />
                <Label for="remember"> {{ $t('Remember me') }} </Label>
            </div>

            <Button type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('Log in') }}
            </Button>
        </div>
    </form>
</template>
