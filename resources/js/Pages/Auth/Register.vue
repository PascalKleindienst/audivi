<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import type { SharedProps } from '@/types/inertia';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const pageProps = computed(() => usePage<SharedProps>().props);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation')
    });
};
</script>

<template>
    <Head :title="$t('Create Account')" />

    <AuthLayout :title="$t('Create Account')">
        <form class="space-y-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">{{ $t('Name') }}</Label>
                <Input id="name" v-model="form.name" type="text" required autofocus autocomplete="name" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ $t('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" required autocomplete="username" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ $t('Password') }}</Label>
                <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation"> {{ $t('Confirm Password') }}</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div v-if="pageProps.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <Label for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            {{ $t('auth.register.agree') }}
                            <a
                                target="_blank"
                                :href="route('terms.show')"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                                >{{ $t('auth.register.terms') }}</a
                            >
                            {{ $t('general.and') }}
                            <a
                                target="_blank"
                                :href="route('policy.show')"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                                >{{ $t('auth.register.privacy') }}</a
                            >
                        </div>
                    </div>
                    <InputError :message="form.errors.terms" />
                </Label>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    {{ $t('Already registered?') }}
                </Link>

                <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('Register') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
