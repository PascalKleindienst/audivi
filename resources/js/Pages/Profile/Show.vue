<script setup lang="ts">
import PageHeader from '@/Components/PageHeader.vue';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';
import {
    DeleteUserForm,
    LogoutOtherBrowserSessionsForm,
    TwoFactorAuthenticationForm,
    UpdatePasswordForm,
    UpdateProfileInformationForm
} from '@/Pages/Profile/Partials';
import { SharedProps } from '@/types/inertia';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

const sidebarNavItems = [
    {
        title: 'profile.profile',
        id: 'settings.profile'
    },
    {
        title: 'profile.password',
        id: 'settings.password'
    },
    {
        title: 'profile.security',
        id: 'settings.security'
    }
];
const currentTab = ref(sidebarNavItems[0].id);
</script>

<template>
    <Head :title="$t('general.profile')" />

    <div class="px-4 py-6">
        <PageHeader title="profile.settings.title" subtitle="profile.settings.subtitle" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
            <aside class="w-full max-w-2xl lg:w-52">
                <nav class="space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.id"
                        :variant="currentTab === item.id ? 'default' : 'ghost'"
                        class="block w-full text-left"
                        @click="currentTab = item.id"
                    >
                        {{ $t(item.title) }}
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <section class="flex-1 space-y-12 md:max-w-4xl">
                <div v-if="currentTab === 'settings.profile'" class="space-y-12">
                    <UpdateProfileInformationForm v-if="page.jetstream.canUpdateProfileInformation" :user="page.auth.user" />
                    <DeleteUserForm v-if="page.jetstream.hasAccountDeletionFeatures" class="mt-10 sm:mt-0" />
                </div>

                <UpdatePasswordForm v-if="page.jetstream.canUpdatePassword && currentTab === 'settings.password'" />
                <div v-if="currentTab === 'settings.security'" class="space-y-12">
                    <TwoFactorAuthenticationForm
                        v-if="page.jetstream.canManageTwoFactorAuthentication"
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                    />

                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />
                </div>
            </section>
        </div>
    </div>
</template>
