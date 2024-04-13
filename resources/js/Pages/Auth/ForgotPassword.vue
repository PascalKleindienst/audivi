<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { trans } from 'laravel-vue-i18n';

defineProps({
    status: String
});

const form = useForm({
    email: ''
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout :title="trans('auth.forgot_password')">
        <AuthenticationCardLogo class="mx-auto" />

        <p class="text-balance text-center text-muted-foreground">
            {{ trans('auth.forgot_password_subtitle') }}
        </p>

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
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Button
                    type="submit"
                    class="w-full"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ trans('auth.password_reset_link') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
