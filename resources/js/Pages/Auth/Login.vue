<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { trans } from 'laravel-vue-i18n';

defineProps<{
    canResetPassword: boolean;
    status?: string;
}>();

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
    <AuthLayout :title="trans('auth.login')">
        <div class="grid gap-2 text-center">
            <AuthenticationCardLogo class="mx-auto" />
            <h1 class="text-3xl font-bold">{{ trans('auth.login') }}</h1>
            <p class="text-balance text-muted-foreground">
                {{ trans('auth.login_subtitle') }}
            </p>
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-4">
                <!-- TODO: Component?-->
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                    {{ status }}
                </div>
                <div class="grid gap-2">
                    <Label for="email">{{ trans('auth.email') }}</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="m@example.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="grid gap-2">
                    <div class="flex items-center">
                        <Label for="password">{{ trans('auth.password') }}</Label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="ml-auto inline-block text-sm underline"
                        >
                            {{ trans('auth.forgot_password') }}
                        </Link>
                    </div>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.password" />
                </div>
                <div class="items-top flex gap-x-2">
                    <Checkbox id="remember" v-model:checked="form.remember" />
                    <Label for="remember"> {{ trans('auth.remember_me') }} </Label>
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ trans('auth.login') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
