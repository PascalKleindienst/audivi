import { InertiaForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';

/**
 * Returns an object containing functions and reactive variables related to two-factor authentication.
 */
export function useTwoFactorAuthentication() {
    const enabling = ref(false);
    const confirming = ref(false);
    const disabling = ref(false);
    const qrCode = ref<string | null>(null);
    const setupKey = ref<string | null>(null);
    const recoveryCodes = ref<Array<string>>([]);

    /**
     * Enables authentication and triggers the necessary actions on success.
     *
     * @param {boolean} requiresConfirmation - Whether confirmation is required after enabling authentication.
     */
    const enableAuthentication = (requiresConfirmation = false) => {
        enabling.value = true;

        router.post(
            route('two-factor.enable'),
            {},
            {
                preserveScroll: true,
                onSuccess: () => Promise.all([showQrCode(), showSetupKey(), showRecoveryCodes()]),
                onFinish: () => {
                    enabling.value = false;
                    confirming.value = requiresConfirmation;
                }
            }
        );
    };

    /**
     * Retrieves the QR code image for two-factor authentication and updates the `qrCode` reactive variable with the SVG data.
     */
    const showQrCode = () => {
        return window.axios.get(route('two-factor.qr-code')).then((response) => {
            qrCode.value = response.data.svg;
        });
    };

    /**
     * Retrieves the setup key for two-factor authentication and updates the `setupKey` reactive variable with the secret key.
     */
    const showSetupKey = () => {
        return window.axios.get(route('two-factor.secret-key')).then((response) => {
            setupKey.value = response.data.secretKey;
        });
    };

    /**
     * Retrieves the recovery codes for two-factor authentication and updates the `recoveryCodes` reactive variable with the response data.
     */
    const showRecoveryCodes = () => {
        return window.axios.get(route('two-factor.recovery-codes')).then((response) => {
            recoveryCodes.value = response.data;
        });
    };

    /**
     * Regenerates the recovery codes for two-factor authentication and updates the `recoveryCodes` reactive variable with the response data.
     */
    const regenerateRecoveryCodes = () => {
        window.axios.post(route('two-factor.recovery-codes')).then(() => showRecoveryCodes());
    };

    /**
     * Confirms the authentication using the provided confirmation form.
     */
    const confirmAuthentication = (confirmationForm: InertiaForm<{ code: string }>) => {
        confirmationForm.post(route('two-factor.confirm'), {
            errorBag: 'confirmTwoFactorAuthentication',
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                confirming.value = false;
                qrCode.value = null;
                setupKey.value = null;
            }
        });
    };

    /**
     * Disables the two-factor authentication and handles the success response.
     */
    const disableAuthentication = () => {
        disabling.value = true;

        router.delete(route('two-factor.disable'), {
            preserveScroll: true,
            onSuccess: () => {
                disabling.value = false;
                confirming.value = false;
            }
        });
    };

    return {
        enabling,
        confirming,
        disabling,
        qrCode,
        setupKey,
        recoveryCodes,
        showQrCode,
        showSetupKey,
        showRecoveryCodes,
        confirmAuthentication,
        regenerateRecoveryCodes,
        enableAuthentication,
        disableAuthentication
    };
}
