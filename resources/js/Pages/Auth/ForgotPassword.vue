<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { AuthLayout, type AuthLayoutProps, useLayout } from '@/Layouts';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status: string;
}>();

defineOptions(
    useLayout<AuthLayoutProps>(AuthLayout, {
        title: 'Forgot your password?',
        description:
            'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.'
    })
);

const form = useForm({
    email: ''
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head :title="$t('Forgot your password?')" />

    <form @submit.prevent="submit">
        <div class="grid gap-4">
            <!-- TODO: Component?-->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                {{ status }}
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ $t('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" placeholder="m@example.com" required autofocus autocomplete="username" />
                <InputError :message="form.errors.email" />
            </div>
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Button type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('Reset Password Notification') }}
            </Button>
        </div>
    </form>
</template>
