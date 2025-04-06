<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import AuthLayout from '@/Layouts/AuthLayout.vue';

import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(defineProps<{ status: string }>(), {
    status: ''
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head :title="$t('Verify Email Address')" />
    <AuthLayout
        :title="$t('Verify Email Address')"
        :description="
            $t(
                'Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.'
            )
        "
    >
        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ $t('A new verification link has been sent to the email address you provided during registration.') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('Resend Verification Email') }}
                </Button>

                <div>
                    <Link
                        :href="route('profile.show')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        {{ $t('Edit Profile') }}
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="ms-2 rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        {{ $t('Log Out') }}
                    </Link>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
