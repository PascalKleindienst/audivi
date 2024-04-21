<script setup lang="ts">
import { route } from 'ziggy-js';
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';

defineOptions({ layout: AuthLayout });

const form = useForm({
    code: '',
    recovery_code: ''
});

const recovery = ref(false);
const recoveryCodeInput = ref<HTMLInputElement | null>(null);
const codeInput = ref<HTMLInputElement | null>(null);

const toggleRecovery = async () => {
    recovery.value = !recovery.value;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value?.focus();
        form.code = '';
    } else {
        codeInput.value?.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head :title="trans('profile.2fa.title')" />

    <div class="grid gap-2 text-center">
        <ApplicationLogo class="mx-auto w-40" />
    </div>

    <form @submit.prevent="submit">
        <div class="grid gap-4">
            <div class="mb-4 text-sm">
                <template v-if="!recovery">
                    {{ trans('auth.2fa.enter_code') }}
                </template>

                <template v-else>
                    {{ trans('auth.2fa.enter_recovery_code') }}
                </template>
            </div>
        </div>
        <div v-if="!recovery">
            <Label for="code">{{ trans('profile.2fa.code') }}</Label>
            <Input
                id="code"
                ref="codeInput"
                v-model="form.code"
                type="text"
                inputmode="numeric"
                class="mt-1 block w-full"
                autofocus
                autocomplete="one-time-code"
            />
            <InputError class="mt-2" :message="form.errors.code" />
        </div>

        <div v-else>
            <Label for="recovery_code">{{ trans('profile.2fa.recovery_code') }}</Label>
            <Input
                id="recovery_code"
                ref="recoveryCodeInput"
                v-model="form.recovery_code"
                type="text"
                class="mt-1 block w-full"
                autocomplete="one-time-code"
            />
            <InputError class="mt-2" :message="form.errors.recovery_code" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Button type="button" variant="link" @click.prevent="toggleRecovery">
                <template v-if="!recovery">{{ trans('profile.2fa.use_recovery_code') }}</template>

                <template v-else>{{ trans('profile.2fa.use_auth_code') }}</template>
            </Button>

            <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ trans('auth.login') }}
            </Button>
        </div>
    </form>
</template>
