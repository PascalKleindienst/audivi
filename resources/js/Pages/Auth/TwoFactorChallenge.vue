<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { AuthLayout, type AuthLayoutProps, useLayout } from '@/Layouts';
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

defineOptions(
    useLayout<AuthLayoutProps>(AuthLayout, {
        title: 'Two Factor Authentication'
    })
);

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
    <Head :title="$t('Two Factor Authentication')" />
    <form @submit.prevent="submit">
        <div class="grid gap-4">
            <div class="mb-4 text-sm">
                <template v-if="!recovery">
                    {{ $t('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </template>

                <template v-else>
                    {{ $t('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                </template>
            </div>
        </div>
        <div v-if="!recovery">
            <Label for="code">{{ $t('Code') }}</Label>
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
            <Label for="recovery_code">{{ $t('Recovery Code') }}</Label>
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
                <template v-if="!recovery">{{ $t('Use a recovery code') }}</template>

                <template v-else>{{ $t('Use an authentication code') }}</template>
            </Button>

            <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('Log in') }}
            </Button>
        </div>
    </form>
</template>
