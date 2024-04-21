<script setup lang="ts">
import type { SharedProps } from '@/types/inertia';
import { computed, watch } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { useForm, usePage } from '@inertiajs/vue3';
import { useTwoFactorAuthentication } from '@/Composables/twoFactorAuthentication';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import InputError from '@/Components/InputError.vue';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';

const props = defineProps<{ requiresConfirmation: boolean }>();
const page = usePage<SharedProps>();

const {
    enabling,
    confirming,
    disabling,
    qrCode,
    setupKey,
    recoveryCodes,
    showRecoveryCodes,
    confirmAuthentication,
    regenerateRecoveryCodes,
    enableAuthentication,
    disableAuthentication
} = useTwoFactorAuthentication();

const confirmationForm = useForm({
    code: ''
});

const twoFactorEnabled = computed(() => !enabling.value && page.props.auth.user?.two_factor_enabled);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});
</script>

<template>
    <ActionSection>
        <template #title>{{ trans('profile.2fa.title') }}</template>

        <template #description>{{ trans('profile.2fa.subtitle') }}</template>

        <template #content>
            <h3 v-if="twoFactorEnabled && !confirming" class="text-lg font-medium">
                {{ trans('profile.2fa.enabled') }}
            </h3>
            <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium">
                {{ trans('profile.2fa.confirming') }}
            </h3>
            <h3 v-else class="text-lg font-medium">
                {{ trans('profile.2fa.disabled') }}
            </h3>

            <div class="mt-3 max-w-xl text-sm text-muted-foreground">
                <p>{{ trans('profile.2fa.information') }}</p>
            </div>

            <!-- Active 2FA -->
            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm">
                        <p v-if="confirming" class="font-semibold">
                            {{ trans('profile.2fa.setup') }}
                        </p>
                        <p v-else>
                            {{ trans('profile.2fa.finished') }}
                        </p>
                    </div>

                    <!-- eslint-disable-next-line vue/no-v-html -->
                    <div class="mt-4 inline-block bg-white p-2" v-html="qrCode" />

                    <div v-if="setupKey" class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                            {{ trans('profile.2fa.setup_key', { key: setupKey }) }}
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4">
                        <Label for="code">{{ trans('profile.2fa.code') }}</Label>

                        <Input
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            class="mt-1 block w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="() => confirmAuthentication(confirmationForm)"
                        />

                        <InputError :message="confirmationForm.errors.code" class="mt-2" />
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && !confirming">
                    <div class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                            {{ trans('profile.2fa.recovery_codes') }}
                        </p>
                    </div>

                    <div class="mt-4 grid max-w-xl gap-1 rounded-lg bg-accent px-4 py-4 font-mono text-sm">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="!twoFactorEnabled">
                    <ConfirmsPassword @confirmed="() => enableAuthentication(props.requiresConfirmation)">
                        <template #trigger>
                            <Button type="button" :class="{ 'opacity-25': enabling }" :disabled="enabling">
                                {{ trans('profile.2fa.enable') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>
                </div>

                <div v-else>
                    <ConfirmsPassword @confirmed="() => confirmAuthentication(confirmationForm)">
                        <template #trigger>
                            <Button
                                v-if="confirming"
                                type="button"
                                class="mb-3 mr-3"
                                :class="{ 'opacity-25': enabling }"
                                :disabled="enabling"
                            >
                                {{ trans('general.confirm') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <template #trigger>
                            <Button
                                v-if="recoveryCodes.length > 0 && !confirming"
                                variant="secondary"
                                class="mb-3 mr-3"
                            >
                                {{ trans('profile.2fa.regenerate_recovery_codes') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <template #trigger>
                            <Button
                                v-if="recoveryCodes.length === 0 && !confirming"
                                variant="secondary"
                                class="mb-3 mr-3"
                            >
                                {{ trans('profile.2fa.show_recovery_codes') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableAuthentication">
                        <template #trigger>
                            <Button
                                v-if="confirming"
                                variant="secondary"
                                :class="{ 'opacity-25': disabling }"
                                :disabled="disabling"
                            >
                                {{ trans('general.cancel') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableAuthentication">
                        <template #trigger>
                            <Button
                                v-if="!confirming"
                                variant="destructive"
                                :class="{ 'opacity-25': disabling }"
                                :disabled="disabling"
                            >
                                {{ trans('profile.2fa.disable') }}
                            </Button>
                        </template>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>
