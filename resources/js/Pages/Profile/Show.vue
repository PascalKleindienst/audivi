<script setup lang="ts">
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { SharedProps } from '@/types/inertia';
import { Separator } from '@/Components/ui/separator';
import PageHeader from '@/Components/PageHeader.vue';

defineProps<{
    confirmsTwoFactorAuthentication: boolean;
    sessions: Array<{
        agent: {
            browser: string;
            is_desktop: boolean;
            platform: string;
        };
        ip_address: string;
        is_current_device: boolean;
        last_active: string;
    }>;
}>();

const page = computed(() => usePage<SharedProps>().props);
</script>

<template>
    <Head title="Profile" />

    <div>
        <PageHeader title="profile.settings.title" subtitle="profile.settings.subtitle" />

        <div v-if="page.jetstream.canUpdateProfileInformation">
            <UpdateProfileInformationForm :user="page.auth.user" />
            <Separator class="my-8" />
        </div>

        <div v-if="page.jetstream.canUpdatePassword">
            <UpdatePasswordForm class="mt-10 sm:mt-0" />
            <Separator class="my-8" />
        </div>

        <div v-if="page.jetstream.canManageTwoFactorAuthentication">
            <TwoFactorAuthenticationForm
                :requires-confirmation="confirmsTwoFactorAuthentication"
                class="mt-10 sm:mt-0"
            />
            <Separator class="my-8" />
        </div>

        <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

        <template v-if="page.jetstream.hasAccountDeletionFeatures">
            <Separator class="my-8" />

            <DeleteUserForm class="mt-10 sm:mt-0" />
        </template>
    </div>
</template>
